<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Asset\AssetTypeRequest;
use App\Models\AssetType;
use App\Models\AssignKvToAsset;
use Illuminate\Http\Request;
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

    public function nextCode(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'asset_type_id' => ['nullable', 'integer'],
        ]);

        return response()->json([
            'code' => AssetType::generateUniqueCodeFromName(
                $validated['name'],
                isset($validated['asset_type_id']) ? (int) $validated['asset_type_id'] : null
            ),
        ]);
    }

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

    public function checkTypeCode(Request $request)
    {
        if (AssetType::where('code', $request->code)->exists())
        {
            return response()->json([
                'success' => false,
                'message' => 'Asset Category Code already exists.',
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Asset Category Code available.',
            ]);
        }
    }
}
