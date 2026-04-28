<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Models\AssignKvToAsset;
use App\Models\KeyVisual;
use App\Models\Store;
use Illuminate\Http\Request;

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
            $validated = $request->validate(['status' => 'required']);
            $assetAssignedKv = AssignKvToAsset::find(request('assigned_asset_kv_id'));
            if ($request->status == 'verified')
            {
                if (!isset($assetAssignedKv->instalation_proof))
                    return response()->json([
                        'success' => false,
                        'message' => 'Kindly Upload proof of installation first',
                    ]);
            }
            $assetAssignedKv->status = $request->status;
            $assetAssignedKv->save();
            return response()->json([
                'success' => true,
                'message' => "Status Changed Successfully to $request->status",
                'data'    => $assetAssignedKv,
            ]);
        } elseif ($request->for == 'proof')
        {
            return response()->json([
                'success' => true,
                'message' => "Kindly Upload proof of installation first",
                'data'    => $request->all(),
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'something went wrong. Please try again later.',
        ]);
    }
}
