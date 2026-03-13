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

class AssetController extends Controller
{
    public function index()
    {
        return view('backend.asset-management.assets', [
            'assets'     => Asset::with(['assetType:id,name', 'store:id,title,code'])->latest()->get(),
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

        return response()->json($query->latest()->get());
    }
}
