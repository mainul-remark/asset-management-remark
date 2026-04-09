<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Asset\AssignAssetToBrandRequest;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssignAssetToBrand;
use App\Models\Brand;
use App\Models\District;
use App\Models\Division;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AssignAssetToBrandController extends Controller
{
    public function index(): View
    {
        return view('backend.asset-management.asset-assign-to-brand', [
            'brands' => Brand::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
            'divisions' => Division::orderBy('name')->get(['id', 'name']),
            'districts' => District::orderBy('name')->get(['id', 'division_id', 'name']),
            'stores' => Store::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('title')
                ->get(['id', 'title', 'code', 'division_id', 'district_id']),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(AssignAssetToBrandRequest $request): JsonResponse
    {
        try {
            $assignments = DB::transaction(fn () => AssignAssetToBrand::createAssignments($request));
            $createdCount = $assignments->count();

            return response()->json([
                'success' => true,
                'message' => $createdCount === 1
                    ? 'Asset assigned to 1 brand successfully.'
                    : "Asset assigned to {$createdCount} brands successfully.",
                'created_count' => $createdCount,
                'data' => $assignments
                    ->map(fn (AssignAssetToBrand $assignment) => $this->transformAssignment($assignment))
                    ->values(),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign asset to brand.',
            ], 500);
        }
    }

    public function filter(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'asset_type_id' => ['nullable', 'exists:asset_types,id'],
            'asset_id' => ['nullable', 'exists:assets,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'status' => ['nullable', 'in:0,1'],
            'is_asset_assigned_currently' => ['nullable', 'in:0,1'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        $paginator = AssignAssetToBrand::filteredQuery($validated)
            ->latest('id')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'data' => $paginator->getCollection()
                ->map(fn (AssignAssetToBrand $assignment) => $this->transformAssignment($assignment))
                ->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    public function assetOptions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'division_id' => ['nullable', 'exists:divisions,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'asset_type_id' => ['nullable', 'exists:asset_types,id'],
            'selected_id' => ['nullable', 'exists:assets,id'],
            'q' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $limit = (int) ($validated['limit'] ?? 30);
        $hasNarrowingInput = collect([
            $validated['division_id'] ?? null,
            $validated['district_id'] ?? null,
            $validated['store_id'] ?? null,
            $validated['asset_type_id'] ?? null,
            $validated['selected_id'] ?? null,
            $validated['q'] ?? null,
        ])->contains(fn ($value) => $value !== null && $value !== '');

        if (!$hasNarrowingInput) {
            return response()->json([
                'data' => [],
                'message' => 'Select filters or search to load assets.',
            ]);
        }

        $query = Asset::query()
            ->with([
                'assetType:id,name',
                'store:id,title,code,division_id,district_id',
                'store.division:id,name',
                'store.district:id,name',
            ])
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->when(!empty($validated['division_id']), function ($query) use ($validated) {
                $query->whereHas('store', fn ($storeQuery) => $storeQuery->where('division_id', $validated['division_id']));
            })
            ->when(!empty($validated['district_id']), function ($query) use ($validated) {
                $query->whereHas('store', fn ($storeQuery) => $storeQuery->where('district_id', $validated['district_id']));
            })
            ->when(!empty($validated['store_id']), fn ($query) => $query->where('store_id', $validated['store_id']))
            ->when(!empty($validated['asset_type_id']), fn ($query) => $query->where('asset_type_id', $validated['asset_type_id']))
            ->when(!empty($validated['q']), function ($query) use ($validated) {
                $query->where(function ($assetQuery) use ($validated) {
                    $assetQuery
                        ->where('name', 'like', '%' . $validated['q'] . '%')
                        ->orWhere('asset_code', 'like', '%' . $validated['q'] . '%');
                });
            })
            ->orderBy('name');

        $assets = $query
            ->limit($limit)
            ->get(['id', 'name', 'asset_code', 'asset_type_id', 'store_id', 'is_common_asset'])
            ->map(fn (Asset $asset) => $this->transformAsset($asset));

        return response()->json([
            'data' => $assets,
            'message' => $assets->isEmpty() ? 'No assets found for the selected filters.' : 'Assets loaded successfully.',
        ]);
    }

    public function show(AssignAssetToBrand $assignAssetToBrand): JsonResponse
    {
        $assignAssetToBrand->loadMissing(AssignAssetToBrand::detailRelations());

        return response()->json($this->transformAssignment($assignAssetToBrand));
    }

    public function edit(AssignAssetToBrand $assignAssetToBrand): JsonResponse
    {
        $assignAssetToBrand->loadMissing(AssignAssetToBrand::detailRelations());

        return response()->json($this->transformAssignment($assignAssetToBrand));
    }

    public function update(AssignAssetToBrandRequest $request, AssignAssetToBrand $assignAssetToBrand): JsonResponse
    {
        try {
            $assignment = DB::transaction(fn () => AssignAssetToBrand::updateOrCreateAssignment($request, $assignAssetToBrand));

            return response()->json([
                'success' => true,
                'message' => 'Asset-to-brand assignment updated successfully.',
                'data' => $this->transformAssignment($assignment),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update asset-to-brand assignment.',
            ], 500);
        }
    }

    public function destroy(AssignAssetToBrand $assignAssetToBrand): JsonResponse
    {
        try {
            DB::transaction(function () use ($assignAssetToBrand) {
                $assignAssetToBrand->markAsNotCurrentlyAssigned();
                $assignAssetToBrand->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Asset-to-brand assignment deleted successfully.',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete asset-to-brand assignment.',
            ], 500);
        }
    }

    private function transformAssignment(AssignAssetToBrand $assignment): array
    {
        $asset = $assignment->asset;

        return [
            'id' => $assignment->id,
            'brand_id' => $assignment->brand_id,
            'asset_id' => $assignment->asset_id,
            'assigned_by_user_id' => $assignment->assigned_by_user_id,
            'asset_charge' => $assignment->asset_charge !== null ? (float) $assignment->asset_charge : null,
            'close_date' => optional($assignment->close_date)->format('Y-m-d'),
            'status' => (int) $assignment->status,
            'is_asset_assigned_currently' => (int) $assignment->is_asset_assigned_currently,
            'created_at' => optional($assignment->created_at)->format('Y-m-d'),
            'brand' => $assignment->brand ? [
                'id' => $assignment->brand->id,
                'name' => $assignment->brand->name,
                'code' => $assignment->brand->code,
            ] : null,
            'asset' => $asset ? $this->transformAsset($asset) : null,
            'assigned_by' => $assignment->assignedBy ? [
                'id' => $assignment->assignedBy->id,
                'name' => $assignment->assignedBy->name,
            ] : null,
        ];
    }

    private function transformAsset(Asset $asset): array
    {
        $assetType = $asset->assetType;
        $store = $asset->store;

        return [
            'id' => $asset->id,
            'name' => $asset->name,
            'asset_code' => $asset->asset_code,
            'asset_type_id' => $asset->asset_type_id,
            'asset_type' => $assetType ? [
                'id' => $assetType->id,
                'name' => $assetType->name,
            ] : null,
            'store_id' => $asset->store_id,
            'is_common_asset' => (int) $asset->is_common_asset,
            'store' => $store ? [
                'id' => $store->id,
                'title' => $store->title,
                'code' => $store->code,
                'division_id' => $store->division_id,
                'district_id' => $store->district_id,
                'division' => $store->division ? [
                    'id' => $store->division->id,
                    'name' => $store->division->name,
                ] : null,
                'district' => $store->district ? [
                    'id' => $store->district->id,
                    'name' => $store->district->name,
                ] : null,
            ] : null,
        ];
    }
}
