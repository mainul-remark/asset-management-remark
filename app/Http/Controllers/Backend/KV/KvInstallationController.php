<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Models\AssignKvToAsset;
use App\Models\Store;
use Illuminate\Http\Request;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class KvInstallationController extends Controller
{
    public function kvInstallation(Request $request)
    {
        return view('backend.kv.kv-installation', [
            'assignedAssetkeyVisuals'   => AssignKvToAsset::with(['asset', 'keyVisual', 'installedBy', 'assignedBy', 'keyVisualFile'])->latest()->get(),
            'stores'                    => Store::where('status', 1)->orderBy('title')->get(['id', 'title', 'slug', 'code']),
        ]);
    }
    public function updateAssignedKvStatusData(Request $request)
    {
        if ($request->for == 'status')
        {
            $validated = $request->validate([
                'status' => 'required|in:pending,planned,installed,verified',
                'assigned_asset_kv_id' => 'required|exists:assign_kv_to_assets,id',
            ]);
            $assetAssignedKv = AssignKvToAsset::find(request('assigned_asset_kv_id'));
            if ($request->status == 'verified')
            {
                if (!isset($assetAssignedKv->instalation_proof))
                    return response()->json([
                        'success' => false,
                        'message' => 'Kindly Upload proof of installation first',
                    ]);
            }
            $assetAssignedKv->instalation_status = $validated['status'];
            $assetAssignedKv->save();
            return response()->json([
                'success' => true,
                'message' => 'Status changed successfully to '.$validated['status'],
                'data'    => $assetAssignedKv,
            ]);
        } elseif ($request->for == 'proof')
        {
            $validated = $request->validate([
                'asset_assign_kv_id' => 'required|exists:assign_kv_to_assets,id',
                'instalation_proof' => 'required|array|min:1',
                'instalation_proof.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            ]);

            $assetAssignedKv = AssignKvToAsset::findOrFail($validated['asset_assign_kv_id']);
            $existingFiles = $assetAssignedKv->instalation_proof
                ? collect(json_decode($assetAssignedKv->instalation_proof, true))
                    ->filter(fn ($path) => is_string($path) && $path !== '')
                    ->values()
                    ->all()
                : [];
            $uploadedFiles = [];

            foreach ($request->file('instalation_proof', []) as $file) {
                $uploadedFiles[] = CustomHelper::fileUpload($file, 'installation-proof', 'installation-proof', 600, 700, null);
            }

            $assetAssignedKv->instalation_proof = json_encode(array_merge($existingFiles, $uploadedFiles));
            $assetAssignedKv->instalation_status = 'installed';
            $assetAssignedKv->save();

            return response()->json([
                'success' => true,
                'message' => 'Installation proof uploaded successfully.',
                'data'    => [
                    'id' => $assetAssignedKv->id,
                    'instalation_proof' => $assetAssignedKv->instalation_proof,
                ],
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'something went wrong. Please try again later.',
        ]);
    }
}
