<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Asset\AssignKvToAssetRequest;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssignKvToAsset;
use App\Models\Brand;
use App\Models\Category;
use App\Models\District;
use App\Models\Division;
use App\Models\KeyVisual;
use App\Models\KeyVisualFiles;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use Throwable;

class AssignKvToAssetController extends Controller
{
    public function index()
    {
        return view('backend.kv.assign-kv-to-asset', [
            'divisions' => Division::orderBy('name')->get(['id', 'name']),
            'districts' => District::orderBy('name')->get(['id', 'division_id', 'name']),
            'stores' => Store::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('title')
                ->get(['id', 'title', 'code', 'division_id', 'district_id']),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name']),
            'assets' => Asset::with(['assetType:id,name', 'store:id,title,code,division_id,district_id'])
                ->where('status', 1)
                ->where('has_kv_slot', 1)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'asset_code', 'asset_type_id', 'store_id']),
            'keyVisuals' => KeyVisual::with([
                'assetType:id,name',
                'brands:id,name,code,logo',
                'categories:id,name,code',
                'keyVisualFiles'
            ])
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'unique_code', 'asset_type_id']),
            'keyVisualFiles' => KeyVisualFiles::with('keyVisualSize:id,name,width,height,unit_name')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'key_visual_id', 'key_visual_size_id', 'kv_file', 'kv_size', 'file_type']),
            'brands' => Brand::whereNull('deleted_at')->orderBy('name')->get(['id', 'name', 'code', 'logo']),
            'categories' => Category::whereNull('deleted_at')->orderBy('name')->get(['id', 'name', 'code']),
            'users' => User::orderBy('name')->get(['id', 'name']),
            'currentUser' => CustomHelper::loggedUser()?->only(['id', 'name']),
            'instalationStatuses' => ['pending', 'planned', 'installed', 'verified'],
        ]);
    }

    public function store(AssignKvToAssetRequest $request): JsonResponse
    {
        if ($slotLimitResponse = $this->validateAvailableKvSlot((int) $request->asset_id)) {
            return $slotLimitResponse;
        }

        try {

            $assignment = DB::transaction(function () use ($request) {
                return $this->persistAssignment($request);
            });

            return response()->json([
                'success' => true,
                'message' => 'KV assigned to asset successfully.',
                'data' => $this->transformAssignment($assignment),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to assign KV to asset.',
            ], 500);
        }
    }

    public function edit(AssignKvToAsset $assignKvToAsset): JsonResponse
    {
        $assignKvToAsset->load($this->assignmentRelations());

        return response()->json($this->transformAssignment($assignKvToAsset));
    }

    public function update(AssignKvToAssetRequest $request, AssignKvToAsset $assignKvToAsset): JsonResponse
    {
        if ($slotLimitResponse = $this->validateAvailableKvSlot((int) $request->asset_id, $assignKvToAsset->id)) {
            return $slotLimitResponse;
        }

        try {
            $assignment = DB::transaction(function () use ($request, $assignKvToAsset) {
                return $this->persistAssignment($request, $assignKvToAsset);
            });

            return response()->json([
                'success' => true,
                'message' => 'KV assignment updated successfully.',
                'data' => $this->transformAssignment($assignment),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update KV assignment.',
            ], 500);
        }
    }

    public function storeAssets(Request $request, Store $store): JsonResponse
    {
        try {
            $assets = $store->assignAssetToStores()
                ->with([
                    'asset:id,name,asset_code,asset_type_id,store_id,status,has_kv_slot',
                    'asset.assetType:id,name',
                    'asset.store:id,title,code,division_id,district_id',
                ])
                ->whereHas('asset', function ($query) use ($store) {
                    $query->where('store_id', $store->id)
                        ->where('status', 1)
                        ->where('has_kv_slot', 1)
                        ->whereNull('deleted_at');
                })
                ->latest('id')
                ->get()
                ->pluck('asset')
                ->filter()
                ->unique('id')
                ->values()
                ->map(function (Asset $asset) {
                    return [
                        'id' => $asset->id,
                        'name' => $asset->name,
                        'asset_code' => $asset->asset_code,
                        'asset_type_id' => $asset->asset_type_id,
                        'asset_type' => $asset->assetType ? [
                            'id' => $asset->assetType->id,
                            'name' => $asset->assetType->name,
                        ] : null,
                        'store_id' => $asset->store_id,
                        'store' => $asset->store ? [
                            'id' => $asset->store->id,
                            'title' => $asset->store->title,
                            'code' => $asset->store->code,
                            'division_id' => $asset->store->division_id,
                            'district_id' => $asset->store->district_id,
                        ] : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'message' => $assets->isEmpty()
                    ? 'No KV-ready assets are assigned to the selected store.'
                    : 'Assets loaded successfully.',
                'data' => $assets,
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load assets for the selected store.',
                'data' => [],
            ], 500);
        }
    }

    public function destroy(AssignKvToAsset $assignKvToAsset): JsonResponse
    {
        try {
            $assignKvToAsset->delete();

            return response()->json([
                'success' => true,
                'message' => 'KV assignment deleted successfully.',
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete KV assignment.',
            ], 500);
        }
    }

    public function filter(Request $request): JsonResponse
    {
        $query = AssignKvToAsset::with($this->assignmentRelations());

        if ($request->filled('division_id')) {
            $query->whereHas('asset.store', fn ($q) => $q->where('division_id', $request->input('division_id')));
        }

        if ($request->filled('district_id')) {
            $query->whereHas('asset.store', fn ($q) => $q->where('district_id', $request->input('district_id')));
        }

        if ($request->filled('store_id')) {
            $query->whereHas('asset', fn ($q) => $q->where('store_id', $request->input('store_id')));
        }

        if ($request->filled('asset_type_id')) {
            $query->whereHas('asset', fn ($q) => $q->where('asset_type_id', $request->input('asset_type_id')));
        }

        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->input('asset_id'));
        }

        if ($request->filled('key_visual_id')) {
            $query->where('key_visual_id', $request->input('key_visual_id'));
        }

        if ($request->filled('key_visual_files_id')) {
            $query->where('key_visual_files_id', $request->input('key_visual_files_id'));
        }

        if ($request->filled('instalation_status')) {
            $query->where('instalation_status', $request->input('instalation_status'));
        }

        if ($request->has('has_perfect_size_kv') && $request->input('has_perfect_size_kv') !== '') {
            $query->where('has_perfect_size_kv', $request->boolean('has_perfect_size_kv') ? 1 : 0);
        }

        return response()->json(
            $query->latest()->get()->map(fn (AssignKvToAsset $assignment) => $this->transformAssignment($assignment))
        );
    }

    private function persistAssignment(AssignKvToAssetRequest $request, ?AssignKvToAsset $assignKvToAsset = null): AssignKvToAsset
    {
        $data = $request->validated();
        $data['has_perfect_size_kv'] = array_key_exists('has_perfect_size_kv', $data)
            ? ($request->boolean('has_perfect_size_kv') ? 1 : 0)
            : ($assignKvToAsset?->has_perfect_size_kv ?? 1);
        $data['assigned_date'] = $data['assigned_date'] ?? $assignKvToAsset?->assigned_date ?? now()->toDateString();
        $data['installed_by'] = array_key_exists('installed_by', $data)
            ? ($data['installed_by'] ?: null)
            : ($assignKvToAsset?->installed_by ?? null);
        $data['instalation_proof'] = $data['instalation_proof'] ?? $assignKvToAsset?->instalation_proof;
        $data['instalation_status'] = $data['instalation_status'] ?? $assignKvToAsset?->instalation_status ?? 'pending';
        $data['instalation_date'] = $data['instalation_date'] ?? $assignKvToAsset?->instalation_date;
        $data['assigned_by'] = $assignKvToAsset?->assigned_by ?? CustomHelper::loggedUser()->id;

        if ($assignKvToAsset) {
            $assignKvToAsset->update($data);

            return $assignKvToAsset->fresh($this->assignmentRelations());
        }

        return AssignKvToAsset::create($data)->load($this->assignmentRelations());
    }

    private function assignmentRelations(): array
    {
        return [
            'asset:id,name,asset_code,asset_type_id,store_id',
            'asset.assetType:id,name',
            'asset.store:id,title,code,division_id,district_id',
            'asset.store.division:id,name',
            'asset.store.district:id,name',
            'keyVisual:id,name,unique_code,asset_type_id',
            'keyVisual.assetType:id,name',
            'keyVisual.brands:id,name,code,logo',
            'keyVisual.categories:id,name,code',
            'keyVisualFile:id,name,key_visual_id,key_visual_size_id,kv_file,kv_size,file_type',
            'keyVisualFile.keyVisualSize:id,name,width,height,unit_name',
            'assignedBy:id,name',
            'installedBy:id,name',
        ];
    }

    private function transformAssignment(AssignKvToAsset $assignment): array
    {
        $asset = $assignment->asset;
        $store = $asset?->store;
        $keyVisual = $assignment->keyVisual;
        $keyVisualFile = $assignment->keyVisualFile;
        $keyVisualSize = $keyVisualFile?->keyVisualSize;

        return [
            'id' => $assignment->id,
            'asset_id' => $assignment->asset_id,
            'key_visual_id' => $assignment->key_visual_id,
            'key_visual_files_id' => $assignment->key_visual_files_id,
            'has_perfect_size_kv' => (int) $assignment->has_perfect_size_kv,
            'assigned_date' => $assignment->assigned_date,
            'assigned_by_id' => $assignment->assigned_by,
            'installed_by_id' => $assignment->installed_by,
            'instalation_proof' => $assignment->instalation_proof,
            'instalation_status' => $assignment->instalation_status,
            'instalation_date' => $assignment->instalation_date,
            'asset' => $asset ? [
                'id' => $asset->id,
                'name' => $asset->name,
                'asset_code' => $asset->asset_code,
                'asset_type_id' => $asset->asset_type_id,
                'asset_type' => $asset->assetType ? [
                    'id' => $asset->assetType->id,
                    'name' => $asset->assetType->name,
                ] : null,
                'store_id' => $asset->store_id,
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
            ] : null,
            'key_visual' => $keyVisual ? [
                'id' => $keyVisual->id,
                'name' => $keyVisual->name,
                'unique_code' => $keyVisual->unique_code,
                'asset_type_id' => $keyVisual->asset_type_id,
                'asset_type' => $keyVisual->assetType ? [
                    'id' => $keyVisual->assetType->id,
                    'name' => $keyVisual->assetType->name,
                ] : null,
                'brands' => $keyVisual->brands->map(fn ($brand) => [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'code' => $brand->code,
                    'logo' => $brand->logo,
                ])->values()->all(),
                'categories' => $keyVisual->categories->map(fn ($category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                ])->values()->all(),
            ] : null,
            'key_visual_file' => $keyVisualFile ? [
                'id' => $keyVisualFile->id,
                'name' => $keyVisualFile->name,
                'key_visual_id' => $keyVisualFile->key_visual_id,
                'key_visual_size_id' => $keyVisualFile->key_visual_size_id,
                'kv_file' => $keyVisualFile->kv_file,
                'kv_size' => $keyVisualFile->kv_size,
                'file_type' => $keyVisualFile->file_type,
                'key_visual_size' => $keyVisualSize ? [
                    'id' => $keyVisualSize->id,
                    'name' => $keyVisualSize->name,
                    'width' => $keyVisualSize->width,
                    'height' => $keyVisualSize->height,
                    'unit_name' => $keyVisualSize->unit_name,
                ] : null,
            ] : null,
            'assigned_by_user' => $assignment->assignedBy ? [
                'id' => $assignment->assignedBy->id,
                'name' => $assignment->assignedBy->name,
            ] : null,
            'installed_by_user' => $assignment->installedBy ? [
                'id' => $assignment->installedBy->id,
                'name' => $assignment->installedBy->name,
            ] : null,
        ];
    }

    private function validateAvailableKvSlot(int $assetId, ?int $ignoreAssignmentId = null): ?JsonResponse
    {
        $asset = Asset::with('assetType:id,has_kv_space,total_kv_slot')->find($assetId);

        if (!$asset?->assetType || (int) $asset->assetType->has_kv_space !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'This asset does not support key visual assignments.',
            ], 422);
        }

        $maxKvSlots = (int) ($asset->assetType->total_kv_slot ?? 0);

        if ($maxKvSlots < 1) {
            return response()->json([
                'success' => false,
                'message' => 'No KV slots are configured for this asset category.',
            ], 422);
        }

        $existingAssignments = AssignKvToAsset::query()
            ->where('asset_id', $assetId)
            ->when($ignoreAssignmentId, fn ($query) => $query->where('id', '!=', $ignoreAssignmentId))
            ->count();

        if ($existingAssignments >= $maxKvSlots) {
            return response()->json([
                'success' => false,
                'message' => 'Max KV slot is reached.',
            ], 422);
        }

        return null;
    }
}
