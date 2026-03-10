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
            'divisions'  => Division::orderBy('name')->get(['id', 'name']),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name']),
        ];
        return CustomHelper::returnDataForWebOrApi($data, 'backend.asset-management.asset-assign-to-store');
    }

    public function storeAssignment(Request $request)
    {
        $request->validate([
            'asset_id'     => 'required|exists:assets,id',
            'store_id'     => 'required|exists:stores,id',
            'assign_date'  => 'required|date',
            'asset_charge' => 'nullable|numeric|min:0',
        ]);

        $assignment = AssignAssetToStore::create([
            'asset_id'            => $request->asset_id,
            'store_id'            => $request->store_id,
            'assigned_by_user_id' => CustomHelper::loggedUser()->id,
            'assign_date'         => $request->assign_date,
            'asset_charge'        => $request->asset_charge ?? 0,
        ]);

        // Also update the asset's store_id
        Asset::where('id', $request->asset_id)->update(['store_id' => $request->store_id]);

        return response()->json(['success' => true, 'message' => 'Asset assigned to store successfully.']);
    }

    public function editAssignment($id)
    {
        $assignment = AssignAssetToStore::with(['asset:id,name,asset_code', 'store:id,title,code'])->findOrFail($id);
        return response()->json($assignment);
    }

    public function updateAssignment(Request $request, $id)
    {
        $assignment = AssignAssetToStore::findOrFail($id);

        $request->validate([
            'asset_id'     => 'required|exists:assets,id',
            'store_id'     => 'required|exists:stores,id',
            'assign_date'  => 'required|date',
            'asset_charge' => 'nullable|numeric|min:0',
        ]);

        $assignment->update([
            'asset_id'     => $request->asset_id,
            'store_id'     => $request->store_id,
            'assign_date'  => $request->assign_date,
            'asset_charge' => $request->asset_charge ?? 0,
        ]);

        // Also update the asset's store_id
        Asset::where('id', $request->asset_id)->update(['store_id' => $request->store_id]);

        return response()->json(['success' => true, 'message' => 'Assignment updated successfully.']);
    }

    public function destroyAssignment($id)
    {
        AssignAssetToStore::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'Assignment deleted successfully.']);
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
