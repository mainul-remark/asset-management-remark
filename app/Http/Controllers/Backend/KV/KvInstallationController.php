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
}
