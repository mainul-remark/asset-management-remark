<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\BillLineItem;
use App\Models\BillPeriod;
use App\Models\CommonSpaceLog;
use App\Models\Store;
use App\Models\StoreBrandBill;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillGenerationService
{
    // sqft conversion factors
    private const SQFT_FACTORS = [
        'ft' => 1.0,
        'in' => 0.00694444,   // 1/144
        'cm' => 0.00107639,   // 1/929.03
        'm'  => 10.7639,
        'mm' => 0.0000107639, // 1/92903
        'yd' => 9.0,
        'px' => 0.0,          // px has no real-world sqft mapping
    ];

    public function generateForPeriod(BillPeriod $period): array
    {
        $period->update(['status' => 'generating']);

        $stats = ['stores' => 0, 'bills' => 0, 'line_items' => 0, 'errors' => []];

        try {
            $stores = Store::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->get();

            foreach ($stores as $store) {
                try {
                    $storeStats = $this->generateForStore($period, $store);
                    if ($storeStats['bills'] > 0) {
                        $stats['stores']++;
                        $stats['bills']      += $storeStats['bills'];
                        $stats['line_items'] += $storeStats['line_items'];
                    }
                } catch (\Throwable $e) {
                    Log::error("BillGeneration: store {$store->id} failed", ['error' => $e->getMessage()]);
                    $stats['errors'][] = "Store {$store->title}: {$e->getMessage()}";
                }
            }

            $period->update([
                'status'       => 'generated',
                'generated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $period->update(['status' => 'open']);
            throw $e;
        }

        return $stats;
    }

    private function generateForStore(BillPeriod $period, Store $store): array
    {
        $stats = ['bills' => 0, 'line_items' => 0];

        // load all active brand assignments for assets in this store
        $activeAssignments = \App\Models\AssignAssetToBrand::query()
            ->whereHas('asset', fn ($q) => $q->where('store_id', $store->id))
            ->where('is_asset_assigned_currently', 1)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->with(['asset.assetType', 'brand'])
            ->get();

        if ($activeAssignments->isEmpty()) {
            return $stats;
        }

        // unique brand ids active in this store
        $brandIds = $activeAssignments->pluck('brand_id')->unique()->filter()->values();

        // purge stale bills from previous runs for brands no longer active in this store
        StoreBrandBill::where('bill_period_id', $period->id)
            ->where('store_id', $store->id)
            ->whereNotIn('brand_id', $brandIds->all())
            ->get()
            ->each(function (StoreBrandBill $bill) {
                $bill->lineItems()->delete();
                $bill->delete();
            });

        // group assignments by brand
        $byBrand = $activeAssignments->groupBy('brand_id');

        // per-brand dedicated ground sqft — used for ratio-based common space distribution
        [$brandDedicatedSqft, $totalDedicatedSqft] = $this->calcBrandDedicatedGroundSqft($brandIds, $byBrand);

        // calculate common space log for this store
        $commonLog = $this->buildCommonSpaceLog($period, $store, $activeAssignments, $brandIds->count());

        $totalCommonCost = round(
            (float) $commonLog->remaining_sqft * (float) $commonLog->rate_per_sqft
            + (float) $commonLog->common_static_fees_total,
            2
        );

        foreach ($brandIds as $brandId) {
            $brandAssignments = $byBrand->get($brandId, collect());
            if ($brandAssignments->isEmpty()) {
                continue;
            }

            $bill = $this->upsertStoreBrandBill($period, $store->id, $brandId);

            // delete old line items (safe re-generation)
            $bill->lineItems()->delete();

            $lineItemCount = 0;

            foreach ($brandAssignments as $assignment) {
                $asset     = $assignment->asset;
                $assetType = $asset?->assetType;

                if (!$asset || !$assetType) {
                    continue;
                }

                $isGround = (int) $assetType->is_ground_type_assets === 1;
                $isCommon = (int) $asset->is_common_asset === 1;

                if ($isCommon) {
                    // common assets (ground or static) go to the common pool — no dedicated line item
                    continue;
                }

                if ($isGround) {
                    $this->createGroundLineItem($bill, $asset, $assetType, $store);
                } else {
                    $this->createStaticLineItem($bill, $asset, $assetType);
                }
                $lineItemCount++;
            }

            // ratio-based common space charge: proportional to brand's dedicated ground sqft
            $brandCommonData = $this->calcBrandCommonCharge(
                $brandDedicatedSqft[$brandId] ?? 0.0,
                $totalDedicatedSqft,
                $brandIds->count(),
                $totalCommonCost,
                (float) $commonLog->remaining_sqft
            );

            if ($brandCommonData['charge'] > 0) {
                $this->createCommonLineItem($bill, $commonLog, $brandCommonData);
                $lineItemCount++;
            }

            $bill->recalculateTotals();

            $stats['bills']++;
            $stats['line_items'] += $lineItemCount;
        }

        return $stats;
    }

    /**
     * Returns [brand_id => dedicated_ground_sqft, total_dedicated_sqft]
     * Only ground + is_common=0 assets count toward the ratio.
     */
    private function calcBrandDedicatedGroundSqft($brandIds, $byBrand): array
    {
        $map   = [];
        $total = 0.0;

        foreach ($brandIds as $brandId) {
            $sqft = 0.0;
            foreach ($byBrand->get($brandId, collect()) as $assignment) {
                $asset     = $assignment->asset;
                $assetType = $asset?->assetType;
                if (!$asset || !$assetType) {
                    continue;
                }
                $isGround = (int) $assetType->is_ground_type_assets === 1;
                $isCommon = (int) $asset->is_common_asset === 1;
                if ($isGround && !$isCommon) {
                    $sqft += $this->getAssetFootprintSqft($assetType);
                }
            }
            $map[$brandId] = round($sqft, 4);
            $total        += $sqft;
        }

        return [$map, round($total, 4)];
    }

    /**
     * Calculate a brand's share of the common space cost by asset-footprint ratio.
     * Falls back to equal distribution when no dedicated ground sqft exists in the store.
     */
    private function calcBrandCommonCharge(
        float $brandSqft,
        float $totalSqft,
        int   $brandCount,
        float $totalCommonCost,
        float $remainingSqft
    ): array {
        if ($totalCommonCost <= 0) {
            return ['charge' => 0.0, 'brand_remaining_sqft' => 0.0];
        }

        if ($totalSqft > 0) {
            $ratio              = $brandSqft / $totalSqft;
            $charge             = round($totalCommonCost * $ratio, 2);
            $brandRemainingSqft = round($remainingSqft * $ratio, 4);
        } else {
            // fallback: equal split when no dedicated ground assets exist in store
            $charge             = $brandCount > 0 ? round($totalCommonCost / $brandCount, 2) : 0.0;
            $brandRemainingSqft = $brandCount > 0 ? round($remainingSqft / $brandCount, 4) : 0.0;
        }

        return ['charge' => $charge, 'brand_remaining_sqft' => $brandRemainingSqft];
    }

    private function buildCommonSpaceLog(BillPeriod $period, Store $store, $allAssignments, int $brandCount): CommonSpaceLog
    {
        $totalStoreSqft = (float) ($store->total_area_sqft ?? 0);
        $ratePerSqft    = (float) ($store->per_sqr_feet_rent ?? 0);

        $dedicatedGroundSqft   = 0.0;
        $commonGroundAssetSqft = 0.0;
        $commonStaticFeesTotal = 0.0;

        // collect all assets in store (unique) — not per brand
        $assetsInStore = $allAssignments
            ->pluck('asset')
            ->filter()
            ->unique('id');

        foreach ($assetsInStore as $asset) {
            $assetType = $asset->assetType;
            if (!$assetType) {
                continue;
            }

            $isGround = (int) $assetType->is_ground_type_assets === 1;
            $isCommon = (int) $asset->is_common_asset === 1;

            if ($isGround) {
                $sqft = $this->getAssetFootprintSqft($assetType);
                if ($isCommon) {
                    $commonGroundAssetSqft += $sqft;
                } else {
                    $dedicatedGroundSqft += $sqft;
                }
            } elseif ($isCommon) {
                // static + is_common_asset = 1 → fee goes to common pool
                $commonStaticFeesTotal += (float) ($asset->minimum_fee ?? 0);
            }
        }

        $remainingSqft        = max(0.0, $totalStoreSqft - $dedicatedGroundSqft);
        $spaceBasedCost       = $remainingSqft * $ratePerSqft;
        $totalCommonCost      = $spaceBasedCost + $commonStaticFeesTotal;
        $commonChargePerBrand = $brandCount > 0 ? round($totalCommonCost / $brandCount, 2) : 0.0;

        return CommonSpaceLog::updateOrCreate(
            ['bill_period_id' => $period->id, 'store_id' => $store->id],
            [
                'total_store_sqft'         => $totalStoreSqft,
                'dedicated_ground_sqft'    => round($dedicatedGroundSqft, 4),
                'common_ground_asset_sqft' => round($commonGroundAssetSqft, 4),
                'remaining_sqft'           => round($remainingSqft, 4),
                'common_static_fees_total' => round($commonStaticFeesTotal, 2),
                'brand_count'              => $brandCount,
                'rate_per_sqft'            => $ratePerSqft,
                'common_charge_per_brand'  => $commonChargePerBrand,
                'calculated_at'            => now(),
            ]
        );
    }

    private function upsertStoreBrandBill(BillPeriod $period, int $storeId, int $brandId): StoreBrandBill
    {
        return StoreBrandBill::firstOrCreate(
            [
                'bill_period_id' => $period->id,
                'store_id'       => $storeId,
                'brand_id'       => $brandId,
            ],
            [
                'ground_amount'     => 0,
                'static_amount'     => 0,
                'common_amount'     => 0,
                'subtotal'          => 0,
                'adjustment_amount' => 0,
                'final_amount'      => 0,
                'bill_status'       => 'draft',
            ]
        );
    }

    private function createGroundLineItem(StoreBrandBill $bill, Asset $asset, AssetType $assetType, Store $store): BillLineItem
    {
        $sqft          = $this->getAssetFootprintSqft($assetType);
        $ratePerSqft   = (float) ($store->per_sqr_feet_rent ?? 0);
        $fullAmount    = round($sqft * $ratePerSqft, 2);
        $brandCount    = $this->countActiveBrandsForAsset($asset->id);
        $perBrandShare = $brandCount > 0 ? round($fullAmount / $brandCount, 2) : $fullAmount;

        return BillLineItem::create([
            'store_brand_bill_id'    => $bill->id,
            'asset_id'               => $asset->id,
            'asset_type_id'          => $assetType->id,
            'payment_type'           => 'ground',
            'asset_sqft'             => $sqft,
            'rate_per_sqft'          => $ratePerSqft,
            'unit_price'             => 0,
            'quantity'               => 1,
            'assigned_brands_count'  => $brandCount,
            'full_calculated_amount' => $fullAmount,
            'calculated_amount'      => $perBrandShare,
            'override_amount'        => null,
            'final_amount'           => $perBrandShare,
        ]);
    }

    private function createStaticLineItem(StoreBrandBill $bill, Asset $asset, AssetType $assetType): BillLineItem
    {
        $minimumFee    = (float) ($asset->minimum_fee ?? 0);
        $brandCount    = $this->countActiveBrandsForAsset($asset->id);
        $perBrandShare = $brandCount > 0 ? round($minimumFee / $brandCount, 2) : $minimumFee;

        return BillLineItem::create([
            'store_brand_bill_id'    => $bill->id,
            'asset_id'               => $asset->id,
            'asset_type_id'          => $assetType->id,
            'payment_type'           => 'static',
            'asset_sqft'             => 0,
            'rate_per_sqft'          => 0,
            'unit_price'             => $minimumFee,
            'quantity'               => 1,
            'assigned_brands_count'  => $brandCount,
            'full_calculated_amount' => $minimumFee,
            'calculated_amount'      => $perBrandShare,
            'override_amount'        => null,
            'final_amount'           => $perBrandShare,
        ]);
    }

    private function createCommonLineItem(StoreBrandBill $bill, CommonSpaceLog $log, array $brandCommonData): BillLineItem
    {
        $brandCharge        = (float) $brandCommonData['charge'];
        $brandRemainingSqft = (float) $brandCommonData['brand_remaining_sqft'];
        $fullCommonCost     = round(
            (float) $log->remaining_sqft * (float) $log->rate_per_sqft + (float) $log->common_static_fees_total,
            2
        );

        return BillLineItem::create([
            'store_brand_bill_id'    => $bill->id,
            'asset_id'               => null,
            'asset_type_id'          => null,
            'payment_type'           => 'common',
            'asset_sqft'             => $brandRemainingSqft,  // brand's allocated share of remaining sqft
            'rate_per_sqft'          => $log->rate_per_sqft,
            'unit_price'             => 0,
            'quantity'               => $brandRemainingSqft,
            'assigned_brands_count'  => $log->brand_count,
            'full_calculated_amount' => $fullCommonCost,      // total common cost across all brands
            'calculated_amount'      => $brandCharge,
            'override_amount'        => null,
            'final_amount'           => $brandCharge,
        ]);
    }

    public function getAssetFootprintSqft(AssetType $assetType): float
    {
        $width  = (float) ($assetType->width ?? 0);
        $height = (float) ($assetType->height ?? 0);
        // $depth = (float) ($assetType->depth ?? 0);

        if ($width <= 0 || $height <= 0) {
            return 0.0;
        }

        $unit   = strtolower(trim($assetType->dimention_unit_name ?? 'ft'));
        $factor = self::SQFT_FACTORS[$unit] ?? 1.0;

        return round($width * $height * $factor, 4);
    }

    private function countActiveBrandsForAsset(int $assetId): int
    {
        return \App\Models\AssignAssetToBrand::query()
            ->where('asset_id', $assetId)
            ->where('is_asset_assigned_currently', 1)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->count();
    }
}
