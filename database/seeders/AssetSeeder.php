<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssignAssetToStore;
use App\Models\Store;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stores = Store::query()
            ->whereBetween('id', [1, 106])
            ->orderBy('id')
            ->get(['id', 'title', 'code']);

        if ($stores->count() < 106) {
            throw new RuntimeException('AssetSeeder requires store ids 1 through 106.');
        }

        $assignedByUserId = User::query()->orderBy('id')->value('id');

        if (! $assignedByUserId) {
            throw new RuntimeException('AssetSeeder requires at least one user for assign_asset_to_stores logs.');
        }

        $assetTypes = $this->resolveAssetTypeIds();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            Asset::truncate();
            AssignAssetToStore::truncate();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $assets = [];
        $assignments = [];
        $assetCodeList = [];
        $baseDate = Carbon::create(2025, 1, 1, 9, 0, 0);

        foreach ($stores as $store) {
            $storeAssetCount = $store->id <= 18 ? 2 : 3;
            $plans = $this->buildStorePlan($store->id, $storeAssetCount);
            $storeCode = strtoupper((string) ($store->code ?: sprintf('S%03d', $store->id)));
            $storeLabel = trim((string) ($store->title ?: 'Store ' . $store->id));

            foreach ($plans as $slotIndex => $templateCode) {
                $template = $this->assetTemplates()[$templateCode];
                $slotNumber = $slotIndex + 1;
                $assetCode = sprintf('HRL-%s-%s-%02d', $storeCode, $templateCode, $slotNumber);
                $assetDate = $baseDate->copy()->addDays((($store->id - 1) * 3) + $slotIndex);

                $assets[] = [
                    'asset_type_id' => $assetTypes[$templateCode],
                    'name' => sprintf('%s - %s', $template['name'], $storeLabel),
                    'default_image' => null,
                    'store_id' => $store->id,
                    'asset_code' => $assetCode,
                    'has_kv_slot' => $template['has_kv_slot'],
                    'minimum_fee' => $template['minimum_fee'],
                    'asset_price' => $template['asset_price'],
                    'is_common_asset' => 0,
                    'planogram_pdf' => null,
                    'status' => 1,
                    'has_self' => $template['has_self'],
                    'total_self' => $template['total_self'],
                    'created_at' => $assetDate,
                    'updated_at' => $assetDate,
                ];

                $assetCodeList[] = $assetCode;

                $assignments[] = [
                    'asset_code' => $assetCode,
                    'store_id' => $store->id,
                    'assigned_by_user_id' => $assignedByUserId,
                    'assign_date' => $assetDate->toDateString(),
                    'asset_charge' => $template['asset_price'] > 0 ? $template['asset_price'] : $template['minimum_fee'],
                    'created_at' => $assetDate,
                    'updated_at' => $assetDate,
                ];
            }
        }

        Asset::query()->insert($assets);

        $assetIdMap = Asset::query()
            ->whereIn('asset_code', $assetCodeList)
            ->pluck('id', 'asset_code')
            ->all();

        $assignmentRows = [];

        foreach ($assignments as $assignment) {
            $assignmentRows[] = [
                'asset_id' => $assetIdMap[$assignment['asset_code']] ?? null,
                'store_id' => $assignment['store_id'],
                'assigned_by_user_id' => $assignment['assigned_by_user_id'],
                'assign_date' => $assignment['assign_date'],
                'asset_charge' => $assignment['asset_charge'],
                'created_at' => $assignment['created_at'],
                'updated_at' => $assignment['updated_at'],
            ];
        }

        AssignAssetToStore::query()->insert($assignmentRows);
    }

    protected function resolveAssetTypeIds(): array
    {
        $codes = array_keys($this->assetTemplates());

        $assetTypeIds = AssetType::query()
            ->whereIn('code', $codes)
            ->pluck('id', 'code')
            ->all();

        $missingCodes = array_values(array_diff($codes, array_keys($assetTypeIds)));

        if ($missingCodes !== []) {
            throw new RuntimeException('Missing asset type rows for codes: ' . implode(', ', $missingCodes));
        }

        return $assetTypeIds;
    }

    protected function buildStorePlan(int $storeId, int $assetCount): array
    {
        $groundCodes = ['GON', 'ECD', 'CDU', 'WDR'];
        $staticCodes = ['BBD', 'BAN', 'LTB', 'WND', 'SHB', 'STD'];
        $digitalCodes = ['LED', 'LCD'];

        $plan = [
            $groundCodes[($storeId - 1) % count($groundCodes)],
            $staticCodes[($storeId - 1) % count($staticCodes)],
        ];

        if ($assetCount === 3) {
            $plan[] = $digitalCodes[($storeId - 1) % count($digitalCodes)];
        }

        return $plan;
    }

    protected function assetTemplates(): array
    {
        return [
            'GON' => [
                'name' => 'Gondola',
                'minimum_fee' => 0,
                'asset_price' => 0,
                'has_kv_slot' => 1,
                'has_self' => 1,
                'total_self' => 4,
            ],
            'ECD' => [
                'name' => 'End Cap Display',
                'minimum_fee' => 0,
                'asset_price' => 0,
                'has_kv_slot' => 1,
                'has_self' => 1,
                'total_self' => 3,
            ],
            'CDU' => [
                'name' => 'Counter Display Unit',
                'minimum_fee' => 0,
                'asset_price' => 0,
                'has_kv_slot' => 1,
                'has_self' => 1,
                'total_self' => 2,
            ],
            'WDR' => [
                'name' => 'Wall Display Rack',
                'minimum_fee' => 0,
                'asset_price' => 0,
                'has_kv_slot' => 1,
                'has_self' => 1,
                'total_self' => 5,
            ],
            'BBD' => [
                'name' => 'Billboard',
                'minimum_fee' => 18000,
                'asset_price' => 18000,
                'has_kv_slot' => 1,
                'has_self' => 0,
                'total_self' => null,
            ],
            'BAN' => [
                'name' => 'Banner',
                'minimum_fee' => 7000,
                'asset_price' => 7000,
                'has_kv_slot' => 1,
                'has_self' => 0,
                'total_self' => null,
            ],
            'LTB' => [
                'name' => 'Light Box',
                'minimum_fee' => 12000,
                'asset_price' => 12000,
                'has_kv_slot' => 1,
                'has_self' => 0,
                'total_self' => null,
            ],
            'WND' => [
                'name' => 'Window Display',
                'minimum_fee' => 10000,
                'asset_price' => 10000,
                'has_kv_slot' => 1,
                'has_self' => 0,
                'total_self' => null,
            ],
            'SHB' => [
                'name' => 'Shelf Branding Strip',
                'minimum_fee' => 3500,
                'asset_price' => 3500,
                'has_kv_slot' => 1,
                'has_self' => 0,
                'total_self' => null,
            ],
            'STD' => [
                'name' => 'Standee',
                'minimum_fee' => 9000,
                'asset_price' => 9000,
                'has_kv_slot' => 1,
                'has_self' => 0,
                'total_self' => null,
            ],
            'LED' => [
                'name' => 'LED TV',
                'minimum_fee' => 30000,
                'asset_price' => 30000,
                'has_kv_slot' => 1,
                'has_self' => 0,
                'total_self' => null,
            ],
            'LCD' => [
                'name' => 'LCD TV',
                'minimum_fee' => 24000,
                'asset_price' => 24000,
                'has_kv_slot' => 1,
                'has_self' => 0,
                'total_self' => null,
            ],
        ];
    }
}
