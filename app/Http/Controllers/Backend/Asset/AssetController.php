<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Asset\AssignAssetToBrandController;
use App\Http\Controllers\Backend\Asset\AssetImportController;
use App\Http\Controllers\Backend\Asset\AssignKvToAssetController;
use App\Http\Requests\Backend\Asset\AssetRequest;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssignAssetToBrand;
use App\Models\AssignAssetToStore;
use App\Models\Brand;
use App\Models\District;
use App\Models\Division;
use App\Models\KeyVisual;
use App\Models\KeyVisualFiles;
use App\Models\PlanogramHistory;
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
        $canView        = allowed([self::class, 'show']);
        $canEdit        = allowed([self::class, 'edit']);
        $canDelete      = allowed([self::class, 'destroy']);
        $canBrandAssign = allowed([AssignAssetToBrandController::class, 'store']);
        $canKvAssign    = allowed([AssignKvToAssetController::class, 'store']);

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
                ->addColumn('actions', function ($asset) use ($canView, $canEdit, $canDelete, $canBrandAssign, $canKvAssign) {
                    $id   = (int) $asset->id;
                    $name = e($asset->name);
                    $html = '<div class="btn-list">';
                    if ($canBrandAssign) {
                        $html .= '<button class="btn btn-icon btn-sm btn-info-light btn-wave open-brand-assign-form" data-id="'.$id.'" title="View brands"><i class="ri-emphasis"></i></button>';
                    }
                    if ($canKvAssign) {
                        $html .= '<button class="btn btn-icon btn-sm btn-info-light btn-wave open-kv-assign-form" data-id="'.$id.'" title="View KV"><i class="ri-tv-line"></i></button>';
                    }
                    if ($canView) {
                        $html .= '<button class="btn btn-icon btn-sm btn-info-light btn-wave btn-view" data-id="'.$id.'" title="View"><i class="ri-eye-line"></i></button>';
                    }
                    if ($canEdit) {
                        $html .= '<button class="btn btn-icon btn-sm btn-primary-light btn-wave btn-edit" data-id="'.$id.'" title="Edit"><i class="ri-edit-box-line"></i></button>';
                    }
                    if ($canDelete) {
                        $html .= '<button class="btn btn-icon btn-sm btn-danger-light btn-wave btn-delete" data-id="'.$id.'" data-name="'.$name.'" title="Delete"><i class="ri-delete-bin-line"></i></button>';
                    }
                    $html .= '</div>';
                    return $html;
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
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name', 'need_asset_image', 'need_asset_planogram', 'has_asset_self', 'is_digital', 'total_self', 'has_kv_space', 'total_kv_slot']),
            'stores'     => Store::orderBy('title')->get(['id', 'title', 'code']),
            'brands' => Brand::query()
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'code']),
            'keyVisuals' => KeyVisual::with(['brands:id,name,code', 'categories:id,name,code'])
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'unique_code', 'asset_type_id']),
            'keyVisualFiles' => KeyVisualFiles::with('keyVisualSize:id,name,width,height,unit_name')
                ->where('status', 1)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'key_visual_id', 'key_visual_size_id', 'kv_file', 'kv_size', 'file_type']),
            'permissions' => [
                'canCreate'      => allowed([self::class, 'store']),
                'canView'        => $canView,
                'canEdit'        => $canEdit,
                'canDelete'      => $canDelete,
                'canBrandAssign' => $canBrandAssign,
                'canKvAssign'    => $canKvAssign,
                'canImport'      => allowed([AssetImportController::class, 'import']),
            ],
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
                if ($asset->planogram_pdf) {
                    $brandIds = array_filter((array) ($request->planogram_brand_id ?? []));
                    if (! empty($brandIds)) {
                        foreach ($brandIds as $brand_id) {
                            PlanogramHistory::recordForAsset($asset, $asset->planogram_pdf, (int) $brand_id);
                        }
                    } else {
                        PlanogramHistory::recordForAsset($asset, $asset->planogram_pdf);
                    }
                }
            }
            if ($asset)
                $asset->assetTypes()->sync($request->asset_type_id);
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
            Asset::with(['assetType:id,name,total_kv_slot,is_digital,total_self,has_kv_space,need_asset_planogram,has_asset_self', 'store:id,title,code'])->findOrFail($id)
        );
    }

    public function edit(string $id)
    {
        $asset = Asset::with(['assetTypes'])->findOrFail($id);
//        return response()->json(Asset::findOrFail($id));
        return response()->json($asset);
    }

    public function update(AssetRequest $request, string $id)
    {
        $asset = Asset::findOrFail($id);
        $oldStoreId      = $asset->store_id;
        $oldPlanogramPdf = $asset->planogram_pdf;

        $asset = DB::transaction(function () use ($request, $asset, $oldStoreId, $oldPlanogramPdf) {
            $asset = Asset::updateOrCreateAsset($request, $asset);
            if ($asset->store_id && $asset->store_id != $oldStoreId) {
                AssignAssetToStore::assignAssetsToStoreLog($asset);
            }
            if ($asset->store_id && $asset->planogram_pdf && $asset->planogram_pdf !== $oldPlanogramPdf) {
                $brandIds = array_filter((array) ($request->planogram_brand_id ?? []));
                if (! empty($brandIds)) {
                    foreach ($brandIds as $brand_id) {
                        PlanogramHistory::recordForAsset($asset, $asset->planogram_pdf, (int) $brand_id);
                    }
                } else {
                    PlanogramHistory::recordForAsset($asset, $asset->planogram_pdf);
                }
            }
            if ($asset)
                $asset->assetTypes()->sync($request->asset_type_id);
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
            'districts'  => District::orderBy('name')->get(['id', 'name']),
            'stores'     => Store::orderBy('title')->get(['id', 'title', 'code']),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name', 'need_asset_image', 'need_asset_planogram', 'has_asset_self', 'is_digital', 'total_self', 'has_kv_space']),
        ];

        return CustomHelper::returnDataForWebOrApi($data, 'backend.asset-management.asset-assign-to-store');
        return view( 'backend.asset-management.asset-assign-to-store');
    }

    public function assignAssetsDatatable(Request $request)
    {
        $filters = $this->validateAssignAssetFilters($request);

        $query = AssignAssetToStore::query()
            ->leftJoin('assets', 'assets.id', '=', 'assign_asset_to_stores.asset_id')
            ->leftJoin('asset_types', 'asset_types.id', '=', 'assets.asset_type_id')
            ->leftJoin('stores', 'stores.id', '=', 'assign_asset_to_stores.store_id')
            ->leftJoin('divisions', 'divisions.id', '=', 'stores.division_id')
            ->leftJoin('districts', 'districts.id', '=', 'stores.district_id')
            ->leftJoin('users', 'users.id', '=', 'assign_asset_to_stores.assigned_by_user_id')
            ->leftJoin('assign_kv_to_assets', function ($join) {
                $join->on('assign_kv_to_assets.asset_id', '=', 'assets.id')
                     ->whereNull('assign_kv_to_assets.deleted_at');
            })
            ->leftJoin('key_visuals', 'key_visuals.id', '=', 'assign_kv_to_assets.key_visual_id')
            ->select([
                'assign_asset_to_stores.id',
                'assign_asset_to_stores.store_id',
                'assign_asset_to_stores.asset_id',
                'assign_asset_to_stores.assign_date',
                'assets.name as asset_name',
                'assets.asset_code',
                'asset_types.name as asset_type_name',
                'stores.title as store_title',
                'stores.code as store_code',
                'divisions.name as division_name',
                'districts.name as district_name',
                'users.name as assigned_by_name',
                'assign_kv_to_assets.id as akv_id',
                'key_visuals.name as kv_name',
                'key_visuals.unique_code as kv_code',
                'assign_kv_to_assets.instalation_status as kv_status',
            ])
            ->whereNull('assets.deleted_at')
            ->whereNull('stores.deleted_at')
            ->when(! empty($filters['division_id']), fn ($q) => $q->where('stores.division_id', $filters['division_id']))
            ->when(! empty($filters['district_id']), fn ($q) => $q->where('stores.district_id', $filters['district_id']))
            ->when(! empty($filters['store_id']), fn ($q) => $q->where('assign_asset_to_stores.store_id', $filters['store_id']))
            ->when(! empty($filters['asset_type_id']), fn ($q) => $q->where('assets.asset_type_id', $filters['asset_type_id']))
            ->when(! empty($filters['asset_id']), fn ($q) => $q->where('assign_asset_to_stores.asset_id', $filters['asset_id']))
            ->when(! empty($filters['assigned_from']), fn ($q) => $q->whereDate('assign_asset_to_stores.assign_date', '>=', $filters['assigned_from']))
            ->when(! empty($filters['assigned_to']), fn ($q) => $q->whereDate('assign_asset_to_stores.assign_date', '<=', $filters['assigned_to']));

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->addColumn('store_group', function ($row) {
                $storeId = (int) ($row->store_id ?? 0);
                $storeTitle = strtolower((string) ($row->store_title ?? 'unassigned'));

                return "{$storeId}|{$storeTitle}";
            })
            ->addColumn('store_summary', function ($row) {
                $storeName = e($row->store_title ?? 'Unassigned');
                $storeCode = filled($row->store_code)
                    ? '<span class="store-group-code">(' . e($row->store_code) . ')</span>'
                    : '';

                $location = collect([$row->division_name, $row->district_name])
                    ->filter()
                    ->implode(', ');

                $locationHtml = $location !== ''
                    ? '<div class="store-group-location"><i class="ri-map-pin-2-line"></i><span>' . e($location) . '</span></div>'
                    : '';

                return '<div class="store-group-heading">'
                    . '<div class="store-group-name"><i class="ri-store-2-line"></i><span>' . $storeName . '</span>' . $storeCode . '</div>'
                    . $locationHtml
                    . '</div>';
            })
            ->addColumn('asset_display', function ($row) {
                $name = e($row->asset_name ?? '-');
                $code = filled($row->asset_code)
                    ? '<small class="asset-code d-block">' . e($row->asset_code) . '</small>'
                    : '';
                $category = filled($row->asset_type_name)
                    ? '<small class="asset-code d-block">' . e($row->asset_type_name) . '</small>'
                    : '';

                return '<div class="asset-name">' . $name . '</div>' . $code . $category;
            })
            ->addColumn('asset_group', function ($row) {
                $storeId = (int) ($row->store_id ?? 0);
                $assetId = (int) ($row->asset_id ?? 0);

                return "{$storeId}|{$assetId}";
            })
            ->addColumn('kv_display', function ($row) {
                if (blank($row->kv_name)) {
                    return '<span class="text-muted fs-12">No KV assigned</span>';
                }

                $name = e($row->kv_name);
                $code = filled($row->kv_code)
                    ? '<small class="kv-code d-block">' . e($row->kv_code) . '</small>'
                    : '';
                $status = filled($row->kv_status)
                    ? '<span class="badge bg-' . match ($row->kv_status) {
                        'installed' => 'success',
                        'verified'  => 'info',
                        'planned'   => 'warning',
                        default     => 'secondary',
                    } . '-transparent fs-10">' . e(ucfirst($row->kv_status)) . '</span>'
                    : '';

                return '<div class="kv-info">'
                    . '<div class="kv-name">' . $name . '</div>'
                    . $code . $status
                    . '</div>';
            })
            ->addColumn('category_display', fn ($row) => e($row->asset_type_name ?? '-'))
            ->addColumn('assign_date_display', function ($row) {
                if (! filled($row->assign_date)) {
                    return '-';
                }

                $timestamp = strtotime((string) $row->assign_date);

                return $timestamp !== false
                    ? date('d M Y', $timestamp)
                    : e((string) $row->assign_date);
            })
            ->addColumn('assigned_by_display', fn ($row) => e($row->assigned_by_name ?? '-'))
            ->filterColumn('store_group', function ($query, $keyword) {
                $query->where(function ($innerQuery) use ($keyword) {
                    $like = "%{$keyword}%";

                    $innerQuery->where('stores.title', 'like', $like)
                        ->orWhere('stores.code', 'like', $like)
                        ->orWhere('divisions.name', 'like', $like)
                        ->orWhere('districts.name', 'like', $like);
                });
            })
            ->filterColumn('asset_display', function ($query, $keyword) {
                $query->where(function ($innerQuery) use ($keyword) {
                    $like = "%{$keyword}%";

                    $innerQuery->where('assets.name', 'like', $like)
                        ->orWhere('assets.asset_code', 'like', $like);
                });
            })
            ->filterColumn('store_summary', function ($query, $keyword) {
                $query->where(function ($innerQuery) use ($keyword) {
                    $like = "%{$keyword}%";

                    $innerQuery->where('stores.title', 'like', $like)
                        ->orWhere('stores.code', 'like', $like)
                        ->orWhere('divisions.name', 'like', $like)
                        ->orWhere('districts.name', 'like', $like);
                });
            })
            ->filterColumn('category_display', fn ($query, $keyword) => $query->where('asset_types.name', 'like', "%{$keyword}%"))
            ->filterColumn('assigned_by_display', fn ($query, $keyword) => $query->where('users.name', 'like', "%{$keyword}%"))
            ->filterColumn('kv_display', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $like = "%{$keyword}%";
                    $q->where('key_visuals.name', 'like', $like)
                      ->orWhere('key_visuals.unique_code', 'like', $like);
                });
            })
            ->orderColumn('store_group', 'stores.title $1')
            ->orderColumn('store_summary', 'stores.title $1')
            ->orderColumn('asset_display', 'assets.name $1')
            ->orderColumn('asset_group', 'assets.name $1')
            ->orderColumn('category_display', 'asset_types.name $1')
            ->orderColumn('assign_date_display', 'assign_asset_to_stores.assign_date $1')
            ->orderColumn('assigned_by_display', 'users.name $1')
            ->orderColumn('kv_display', 'key_visuals.name $1')
            ->rawColumns(['store_summary', 'asset_display', 'kv_display'])
            ->toJson();
    }

    public function nextName(Request $request)
    {
        $request->validate([
            'asset_type_id' => 'required|exists:asset_types,id',
            'store_id'      => 'required|exists:stores,id',
        ]);

        $assetType = AssetType::findOrFail($request->asset_type_id);
        $store     = Store::findOrFail($request->store_id);

        // Use asset_types.code if set, else fall back to first 3 letters of name
        $typeCode  = strtoupper(
            !empty($assetType->code)
                ? $assetType->code
                : substr(preg_replace('/[^a-zA-Z]/', '', $assetType->name), 0, 3)
        );
        $storeCode = strtoupper($store->store_code);
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
        $filters = $this->validateAssignAssetFilters($request);

        $query = AssignAssetToStore::with([
            'asset:id,name,asset_code,asset_type_id,asset_price,status',
            'asset.assetType:id,name',
            'store:id,title,code,division_id,district_id',
            'store.division:id,name',
            'store.district:id,name',
            'assignedBy:id,name',
        ]);

        if (! empty($filters['division_id'])) {
            $query->whereHas('store', fn ($q) => $q->where('division_id', $filters['division_id']));
        }
        if (! empty($filters['district_id'])) {
            $query->whereHas('store', fn ($q) => $q->where('district_id', $filters['district_id']));
        }
        if (! empty($filters['store_id'])) {
            $query->where('store_id', $filters['store_id']);
        }
        if (! empty($filters['asset_type_id'])) {
            $query->whereHas('asset', fn ($q) => $q->where('asset_type_id', $filters['asset_type_id']));
        }
        if (! empty($filters['asset_id'])) {
            $query->where('asset_id', $filters['asset_id']);
        }
        if (! empty($filters['assigned_from'])) {
            $query->whereDate('assign_date', '>=', $filters['assigned_from']);
        }
        if (! empty($filters['assigned_to'])) {
            $query->whereDate('assign_date', '<=', $filters['assigned_to']);
        }

        return response()->json($query->latest()->get());
    }

    private function validateAssignAssetFilters(Request $request): array
    {
        return $request->validate([
            'division_id'   => 'nullable|exists:divisions,id',
            'district_id'   => 'nullable|exists:districts,id',
            'store_id'      => 'nullable|exists:stores,id',
            'asset_type_id' => 'nullable|exists:asset_types,id',
            'asset_id'      => 'nullable|exists:assets,id',
            'assigned_from' => 'nullable|date',
            'assigned_to'   => 'nullable|date|after_or_equal:assigned_from',
        ]);
    }

    public function planogramHistories(Request $request)
    {
        if ($request->ajax()) {
            $query = PlanogramHistory::query()
                ->leftJoin('stores', 'stores.id', '=', 'planogram_histories.store_id')
                ->leftJoin('assets', 'assets.id', '=', 'planogram_histories.asset_id')
                ->leftJoin('brands', 'brands.id', '=', 'planogram_histories.brand_id')
                ->leftJoin('users', 'users.id', '=', 'planogram_histories.assigned_by')
                ->select([
                    'planogram_histories.id',
                    'planogram_histories.store_id',
                    'planogram_histories.asset_id',
                    'planogram_histories.brand_id',
                    'planogram_histories.assigned_by',
                    'planogram_histories.file_path',
                    'planogram_histories.status',
                    'planogram_histories.changed_date',
                    'stores.title as store_title',
                    'stores.code as store_code',
                    'assets.name as asset_name',
                    'brands.name as brand_name',
                    'users.name as assigned_by_name',
                ])
                ->when($request->filled('store_id'), fn ($query) => $query->where('planogram_histories.store_id', $request->integer('store_id')))
                ->when($request->filled('asset_id'), fn ($query) => $query->where('planogram_histories.asset_id', $request->integer('asset_id')))
                ->when($request->filled('brand_id'), fn ($query) => $query->where('planogram_histories.brand_id', $request->integer('brand_id')))
                ->orderBy('stores.title')
                ->orderBy('assets.name')
                ->orderByDesc('planogram_histories.changed_date')
                ->orderBy('brands.name');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('store_display', function (PlanogramHistory $planogramHistory) {
                    $title = e($planogramHistory->store_title ?: 'N/A');
                    $code = $planogramHistory->store_code
                        ? '<div class="text-muted small">' . e($planogramHistory->store_code) . '</div>'
                        : '';

                    return <<<HTML
<div class="planogram-cell">
    <div class="fw-semibold">{$title}</div>
    {$code}
</div>
HTML;
                })
                ->addColumn('asset_display', function (PlanogramHistory $planogramHistory) {
                    $name = e($planogramHistory->asset_name ?: 'N/A');
                    $changedAt = $planogramHistory->changed_date
                        ? Carbon::parse($planogramHistory->changed_date)->format('d M Y, h:i A')
                        : 'N/A';

                    return <<<HTML
<div class="planogram-cell">
    <div class="fw-semibold">{$name}</div>
    <div class="text-muted small">Changed: {$changedAt}</div>
</div>
HTML;
                })
                ->addColumn('brand_display', function (PlanogramHistory $planogramHistory) {
                    if (! $planogramHistory->brand_name) {
                        return '<span class="text-muted">No brand</span>';
                    }

                    return '<span class="badge bg-primary-transparent text-primary">' . e($planogramHistory->brand_name) . '</span>';
                })
                ->addColumn('assigned_by_display', function (PlanogramHistory $planogramHistory) {
                    return e($planogramHistory->assigned_by_name ?: 'System');
                })
                ->addColumn('status_display', function (PlanogramHistory $planogramHistory) {
                    $isActive = (int) $planogramHistory->status === 1;
                    $classes = $isActive
                        ? 'bg-success-transparent text-success'
                        : 'bg-danger-transparent text-danger';
                    $label = $isActive ? 'Active' : 'Inactive';

                    return '<span class="badge ' . $classes . '">' . $label . '</span>';
                })
                ->addColumn('actions', function (PlanogramHistory $planogramHistory) {
                    if (! $planogramHistory->file_path) {
                        return '<span class="text-muted">No file</span>';
                    }

                    $fileUrl = e(asset($planogramHistory->file_path));
                    $storeTitle = e($planogramHistory->store_title ?: 'Store');
                    $assetName = e($planogramHistory->asset_name ?: 'Asset');

                    return <<<HTML
<div class="btn-list">
    <button
        type="button"
        class="btn btn-icon btn-sm btn-info-light btn-wave btn-view"
        data-file-url="{$fileUrl}"
        data-store-title="{$storeTitle}"
        data-asset-name="{$assetName}"
        title="View Planogram"
    >
        <i class="ri-eye-line"></i>
    </button>
</div>
HTML;
                })
                ->addColumn('store_group', function (PlanogramHistory $planogramHistory) {
                    return (string) ($planogramHistory->store_id ?: 'store-' . $planogramHistory->id);
                })
                ->addColumn('asset_group', function (PlanogramHistory $planogramHistory) {
                    $changedAt = $planogramHistory->changed_date
                        ? Carbon::parse($planogramHistory->changed_date)->format('Y-m-d H:i:s')
                        : 'no-date';

                    return md5(implode('|', [
                        $planogramHistory->store_id ?: 'no-store',
                        $planogramHistory->asset_id ?: 'no-asset',
                        $planogramHistory->file_path ?: 'no-file',
                        $planogramHistory->assigned_by ?: 'no-user',
                        (int) $planogramHistory->status,
                        $changedAt,
                    ]));
                })
                ->rawColumns([
                    'store_display',
                    'asset_display',
                    'brand_display',
                    'status_display',
                    'actions',
                ])
                ->toJson();
        }

        return view('backend.asset-management.planogram-history', [
            'stores' => Store::where('status', 1)->orderBy('title')->get(['id', 'title', 'code']),
            'assets' => Asset::where('status', 1)->orderBy('name')->get(['id', 'name', 'asset_code']),
            'brands' => Brand::where('status', 1)->orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }
}
