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
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class AssetController extends Controller
{
    public function index()
    {
        return view('backend.asset-management.assets', [
            'assets'     => Asset::with(['assetType:id,name', 'store:id,title,code'])->latest()->get(),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name']),
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
            'divisions' => Division::get(['id', 'name']),
            'stores'    => Store::latest()->get(['id', 'title']),
        ];
        return CustomHelper::returnDataForWebOrApi($data, 'backend.asset-management.asset-assign-to-store');
    }
}
