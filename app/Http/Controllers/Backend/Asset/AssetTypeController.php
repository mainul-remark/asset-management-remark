<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Asset\AssetTypeRequest;
use App\Models\AssetType;
use App\Models\AssignKvToAsset;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class AssetTypeController extends Controller
{
    public function index()
    {
        return view('backend.asset-management.asset-type', [
            'assetTypes' => AssetType::query()
                ->orderBy('name')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function create() {}

    public function store(AssetTypeRequest $request)
    {
        $assetType = AssetType::updateOrCreateAssetType($request);
        return response()->json([
            'message' => 'Asset type created successfully.',
            'data'    => $assetType,
        ]);
    }

    public function show(AssetType $assetType)
    {
        return response()->json($assetType);
    }

    public function edit(AssetType $assetType)
    {
        return response()->json($assetType);
    }

    public function update(AssetTypeRequest $request, AssetType $assetType)
    {

        AssetType::updateOrCreateAssetType($request, $assetType);

        return response()->json([
            'message' => 'Asset type updated successfully.',
            'data'    => $assetType->fresh(),
        ]);
    }

    public function destroy(AssetType $assetType)
    {
//        $assetType->deleteWithImage();
        $assetType->delete();
        return response()->json([
            'message' => 'Asset type deleted successfully.',
        ]);
    }
}
