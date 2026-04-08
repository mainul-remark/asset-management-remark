<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Asset\AssetRequest;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssignAssetToStore;
use App\Models\Division;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use Yajra\DataTables\Facades\DataTables;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Asset::query()
                ->leftJoin('asset_types', 'asset_types.id', '=', 'assets.asset_type_id')
                ->leftJoin('stores', 'stores.id', '=', 'assets.store_id')
                ->select([
                    'assets.id',
                    'assets.name',
                    'assets.asset_code',
                    'assets.default_image',
                    'assets.store_id',
                    'assets.asset_type_id',
                    'assets.has_kv_slot',
                    'assets.has_self',
                    'assets.total_self',
                    'assets.is_common_asset',
                    'assets.status',
                    'asset_types.name as asset_type_name',
                    'stores.title as store_title',
                    'stores.code as store_code',
                ])
                ->when($request->filled('store_id'), fn ($q) => $q->where('assets.store_id', $request->integer('store_id')))
                ->when($request->filled('asset_type_id'), fn ($q) => $q->where('assets.asset_type_id', $request->integer('asset_type_id')))
                ->latest('assets.id');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('image', function ($asset) {
                    if ($asset->default_image) {
                        $src = asset($asset->default_image);
                        $alt = e($asset->name);

                        return "<img class=\"asset-thumb\" src=\"{$src}\" alt=\"{$alt}\">";
                    }

                    return '<div class="asset-thumb-empty"><i class="ri-image-line"></i></div>';
                })
                ->addColumn('name_display', function ($asset) {
                    $name = '<div class="fw-semibold">'.e($asset->name).'</div>';
                    $badges = [];

                    if ((int) $asset->has_kv_slot === 1) {
                        $badges[] = '<span class="badge bg-warning-transparent">KV Slot</span>';
                    }

                    if ((int) $asset->has_self === 1) {
                        $totalSelf = (int) ($asset->total_self ?? 0);
                        $badges[] = '<span class="badge bg-info-transparent">'.$totalSelf.' Shelf</span>';
                    }

                    if ($badges === []) {
                        return $name;
                    }

                    return $name.'<div class="mt-1">'.implode(' ', $badges).'</div>';
                })
                ->addColumn('code_display', fn ($asset) => '<code>'.e($asset->asset_code).'</code>')
                ->addColumn('asset_type_display', fn ($asset) => e($asset->asset_type_name ?? '—'))
                ->addColumn('store_display', function ($asset) {
                    if ((int) $asset->is_common_asset === 1) {
                        return '<span class="badge bg-primary-transparent">Common</span>';
                    }

                    return e($asset->store_title ?? '—');
                })
                ->addColumn('status_badge', function ($asset) {
                    if ((int) $asset->status === 1) {
                        return '<span class="badge bg-success-transparent">Active</span>';
                    }

                    return '<span class="badge bg-danger-transparent">Inactive</span>';
                })
                ->addColumn('actions', function ($asset) {
                    $id = (int) $asset->id;
                    $name = e($asset->name);

                    return <<<HTML
<div class="btn-list">
    <button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="{$id}" title="View">
        <i class="ri-eye-line"></i>
    </button>
    <button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="{$id}" title="Edit">
        <i class="ri-edit-box-line"></i>
    </button>
    <button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="{$id}" data-name="{$name}" title="Delete">
        <i class="ri-delete-bin-line"></i>
    </button>
</div>
HTML;
                })
                ->rawColumns([
                    'image',
                    'name_display',
                    'code_display',
                    'asset_type_display',
                    'store_display',
                    'status_badge',
                    'actions',
                ])
                ->toJson();
        }

        return view('backend.asset-management.assets', [
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name', 'need_asset_image', 'need_asset_planogram', 'has_asset_self', 'is_digital', 'total_self', 'has_kv_space']),
            'stores'     => Store::orderBy('title')->get(['id', 'title', 'code']),
        ]);
    }

    public function create()
    {
        return redirect()->route('assets.index');
    }

    public function store(AssetRequest $request)
    {
        $asset = DB::transaction(function () use ($request) {
            $asset = Asset::updateOrCreateAsset($request);
            if ($asset->store_id) {
                AssignAssetToStore::assignAssetsToStoreLog($asset);
            }
            return $asset;
        });


        return response()->json([
            'message' => 'Asset created successfully.',
            'data'    => $asset->load(['assetType:id,name', 'store:id,title,code']),
        ]);
    }

    public function show(string $id)
    {
        return response()->json(
            Asset::with(['assetType:id,name', 'store:id,title,code'])->findOrFail($id)
        );
    }

    public function edit(string $id)
    {
        return response()->json(Asset::findOrFail($id));
    }

    public function update(AssetRequest $request, string $id)
    {
        $asset = Asset::findOrFail($id);
        $oldStoreId = $asset->store_id;

        $asset = DB::transaction(function () use ($request, $asset, $oldStoreId) {
            $asset = Asset::updateOrCreateAsset($request, $asset);
            if ($asset->store_id && $asset->store_id != $oldStoreId) {
                AssignAssetToStore::assignAssetsToStoreLog($asset);
            }
            return $asset;
        });

        return response()->json([
            'message' => 'Asset updated successfully.',
            'data'    => $asset->fresh()->load(['assetType:id,name', 'store:id,title,code']),
        ]);
    }

    public function destroy(string $id)
    {
        Asset::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Asset deleted successfully.',
        ]);
    }

    public function assignAssets(Request $request)
    {
        $data = [
            'divisions'  => Division::orderBy('name')->get(['id', 'name']),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name', 'need_asset_image', 'need_asset_planogram', 'has_asset_self', 'is_digital', 'total_self', 'has_kv_space']),
        ];

        return CustomHelper::returnDataForWebOrApi($data, 'backend.asset-management.asset-assign-to-store');
        return view( 'backend.asset-management.asset-assign-to-store');
    }

    public function nextName(Request $request)
    {
        $request->validate([
            'asset_type_id' => 'required|exists:asset_types,id',
            'store_id'      => 'required|exists:stores,id',
        ]);

        $assetType = AssetType::findOrFail($request->asset_type_id);
        $store     = Store::findOrFail($request->store_id);

        // 3-char code from asset type name (letters only, uppercase)
        $typeCode  = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $assetType->name), 0, 3));
        $storeCode = strtoupper($store->code);
        $prefix    = $typeCode . '-' . $storeCode . '-';

        $seq = 1;
        while (Asset::withTrashed()->where('name', $prefix . $seq)->exists()) {
            $seq++;
        }

        return response()->json(['name' => $prefix . $seq]);
    }

    public function getAssetsByType($assetTypeId)
    {
        $assets = Asset::select('id', 'name', 'asset_code')
            ->where('asset_type_id', $assetTypeId)
            ->orderBy('name')
            ->get();
        return response()->json($assets);
    }

    public function assignAssetsFilter(Request $request)
    {
        $request->validate([
            'division_id'   => 'nullable|exists:divisions,id',
            'district_id'   => 'nullable|exists:districts,id',
            'store_id'      => 'nullable|exists:stores,id',
            'asset_type_id' => 'nullable|exists:asset_types,id',
            'asset_id'      => 'nullable|exists:assets,id',
            'assigned_from' => 'nullable|date',
            'assigned_to'   => 'nullable|date|after_or_equal:assigned_from',
        ]);

        $query = AssignAssetToStore::with([
            'asset:id,name,asset_code,asset_type_id,asset_price,status',
            'asset.assetType:id,name',
            'store:id,title,code,division_id,district_id',
            'store.division:id,name',
            'store.district:id,name',
            'assignedBy:id,name',
        ]);

        if ($request->filled('division_id')) {
            $query->whereHas('store', fn ($q) => $q->where('division_id', $request->division_id));
        }
        if ($request->filled('district_id')) {
            $query->whereHas('store', fn ($q) => $q->where('district_id', $request->district_id));
        }
        if ($request->filled('store_id')) {
            $query->where('store_id', $request->store_id);
        }
        if ($request->filled('asset_type_id')) {
            $query->whereHas('asset', fn ($q) => $q->where('asset_type_id', $request->asset_type_id));
        }
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }
        if ($request->filled('assigned_from')) {
            $query->whereDate('assign_date', '>=', $request->assigned_from);
        }
        if ($request->filled('assigned_to')) {
            $query->whereDate('assign_date', '<=', $request->assigned_to);
        }

        return response()->json($query->latest()->get());
    }
}
