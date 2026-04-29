<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Models\AssignKvToAsset;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use Yajra\DataTables\Facades\DataTables;

class KvInstallationController extends Controller
{
    public function kvInstallation(Request $request)
    {
        return view('backend.kv.kv-installation', [
            'stores' => Store::where('status', 1)->orderBy('title')->get(['id', 'title', 'slug', 'code']),
        ]);
    }

    public function kvInstallationDatatable(Request $request): JsonResponse
    {
        $query = AssignKvToAsset::query()
            ->leftJoin('assets', 'assign_kv_to_assets.asset_id', '=', 'assets.id')
            ->leftJoin('asset_types', 'assets.asset_type_id', '=', 'asset_types.id')
            ->leftJoin('stores', 'assets.store_id', '=', 'stores.id')
            ->leftJoin('key_visuals', 'assign_kv_to_assets.key_visual_id', '=', 'key_visuals.id')
            ->leftJoin('key_visual_files', 'assign_kv_to_assets.key_visual_files_id', '=', 'key_visual_files.id')
            ->select([
                'assign_kv_to_assets.id',
                'assign_kv_to_assets.instalation_status',
                'assign_kv_to_assets.instalation_proof',
                'assign_kv_to_assets.created_at',
                'stores.title as store_title',
                'stores.code as store_code',
                'stores.address as store_address',
                'asset_types.name as asset_type_name',
                'assets.asset_code',
                'key_visuals.name as key_visual_name',
                'key_visuals.unique_code as key_visual_code',
                'key_visuals.kv_thumb',
                'key_visual_files.kv_file_code',
            ])
            ->whereNull('assign_kv_to_assets.deleted_at')
            ->whereNull('assets.deleted_at')
            ->whereNull('key_visuals.deleted_at')
            ->whereNull('key_visual_files.deleted_at')
            ->when($request->filled('installation_status_filter'), function ($q) use ($request) {
                $q->where('assign_kv_to_assets.instalation_status', $request->string('installation_status_filter')->toString());
            })
            ->when($request->filled('store_id'), function ($q) use ($request) {
                $q->where('stores.id', $request->integer('store_id'));
            })
            ->when($request->filled('search_text'), function ($q) use ($request) {
                $search = trim((string) $request->input('search_text'));

                $q->where(function ($inner) use ($search) {
                    $inner->where('stores.title', 'like', "%{$search}%")
                        ->orWhere('stores.code', 'like', "%{$search}%")
                        ->orWhere('stores.address', 'like', "%{$search}%")
                        ->orWhere('asset_types.name', 'like', "%{$search}%")
                        ->orWhere('assets.asset_code', 'like', "%{$search}%")
                        ->orWhere('key_visuals.name', 'like', "%{$search}%")
                        ->orWhere('key_visuals.unique_code', 'like', "%{$search}%")
                        ->orWhere('key_visual_files.kv_file_code', 'like', "%{$search}%");
                });
            })
            ->latest('assign_kv_to_assets.id');

        return DataTables::eloquent($query)
            ->addColumn('store_name', function ($row) {
                $title = e($row->store_title ?? '');
                $code = filled($row->store_code) ? e($row->store_code) : '';
                $address = e($row->store_address ?? '');
                $meta = trim($code . ($code !== '' && $address !== '' ? ' • ' : '') . $address);

                return '<div class="inst-store-name">'.$title.'</div>'
                    .'<div class="inst-store-meta">'.$meta.'</div>';
            })
            ->addColumn('branding_medium', function ($row) {
                $assetTypeName = e($row->asset_type_name ?? '');
                $assetCode = filled($row->asset_code) ? '<div class="inst-store-meta">'.e($row->asset_code).'</div>' : '';

                return '<div class="fw-semibold" style="font-size:0.85rem;">'.$assetTypeName.'</div>'.$assetCode;
            })
            ->addColumn('kv_id', function ($row) {
                $thumb = $row->kv_thumb
                    ? '<div class="inst-kv-thumb"><img src="'.asset($row->kv_thumb).'" alt="'.e($row->key_visual_name ?? 'KV').'"></div>'
                    : '<div class="inst-kv-thumb d-flex align-items-center justify-content-center"><i class="bi bi-image text-muted"></i></div>';
                $kvCode = e($row->key_visual_code ?? '');
                $fileCode = e($row->kv_file_code ?? '');

                return '<div class="d-flex align-items-center gap-2">'
                    .$thumb
                    .'<div>'
                    .'<span class="inst-kv-id">KV: '.$kvCode.'</span>'
                    .'<span class="inst-badge-new">File: '.$fileCode.'</span>'
                    .'</div>'
                    .'</div>';
            })
            ->addColumn('status', function ($row) {
                $status = (string) ($row->instalation_status ?? 'pending');
                $label = ucfirst($status);
                $icon = match ($status) {
                    'installed' => 'bi-check-circle',
                    'verified' => 'bi-shield-check',
                    default => 'bi-calendar-event',
                };
                $verifiedDisabled = blank($row->instalation_proof) ? 'disabled' : '';

                return '<div class="dropdown">'
                    .'<button class="inst-status-btn '.($status == 'pending' ? 'inst-status-planned' : 'inst-status-installed').' dropdown-toggle" data-bs-toggle="dropdown">'
                    .'<i class="bi '.$icon.' me-1"></i>'.$label
                    .'</button>'
                    .'<ul class="dropdown-menu inst-status-dropdown">'
                    .'<li><a class="dropdown-item inst-status-dd-item '.($status === 'pending' ? 'active' : '').'" href="#" data-status="pending" data-asset-assigned-kv-id="'.$row->id.'"><i class="bi bi-calendar-event me-1"></i>Pending</a></li>'
                    .'<li><a class="dropdown-item inst-status-dd-item '.($status === 'planned' ? 'active' : '').'" href="#" data-status="planned" data-asset-assigned-kv-id="'.$row->id.'"><i class="bi bi-calendar-event me-1"></i>Planned</a></li>'
                    .'<li><a class="dropdown-item inst-status-dd-item '.($status === 'installed' ? 'active' : '').'" href="#" data-status="installed" data-asset-assigned-kv-id="'.$row->id.'"><i class="bi bi-check-circle me-1"></i>Installed</a></li>'
                    .'<li><a class="dropdown-item inst-status-dd-item '.($status === 'verified' ? 'active' : '').' '.$verifiedDisabled.'" href="#" data-status="verified" data-asset-assigned-kv-id="'.$row->id.'"><i class="bi bi-shield-check me-1"></i>Verified</a></li>'
                    .'</ul>'
                    .'</div>';
            })
            ->addColumn('photos', function ($row) {
                if (blank($row->instalation_proof)) {
                    return '<span class="inst-no-photos">No photos</span>';
                }

                $files = collect(json_decode($row->instalation_proof, true))
                    ->filter(fn ($file) => is_string($file) && $file !== '')
                    ->take(3);

                if ($files->isEmpty()) {
                    return '<span class="inst-no-photos">No photos</span>';
                }

                return $files->map(function ($file) use ($row) {
                    return '<div class="inst-photo-thumb m-1"><img src="'.asset($file).'" alt="'.e($row->key_visual_name ?? 'Proof').' proof photo"></div>';
                })->implode('');
            })
            ->addColumn('actions', function ($row) {
                return '<button class="btn-action" data-bs-toggle="modal" data-bs-target="#installationDetailModal"><i class="bi bi-eye"></i></button>'
                    .'<button class="btn-action upload-proof-image" data-asset-assign-kv-id="'.$row->id.'"><i class="bi bi-image"></i></button>';
            })
            ->rawColumns(['store_name', 'branding_medium', 'kv_id', 'status', 'photos', 'actions'])
            ->toJson();
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
