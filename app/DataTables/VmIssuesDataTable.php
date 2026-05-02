<?php

namespace App\DataTables;

use App\Models\VisualMerchandising;
use Illuminate\Database\Eloquent\Builder;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class VmIssuesDataTable extends DataTable
{
    public function dataTable($query): DataTableAbstract
    {
        return datatables()
            ->eloquent($query)
            ->addColumn('store_name',       fn ($row) => $row->store?->title ?? 'N/A')
            ->addColumn('store_code',       fn ($row) => $row->store?->code ?? '')
            ->addColumn('asset_name',       fn ($row) => $row->asset?->name ?? 'N/A')
            ->addColumn('asset_type',       fn ($row) => $row->asset?->assetType?->name ?? '')
            ->addColumn('asset_code',       fn ($row) => $row->asset?->asset_code ?? '')
            ->addColumn('issue_fix_status', fn ($row) => $row->issue_fix_status)
            ->addColumn('issue_preview',    fn ($row) => \Illuminate\Support\Str::limit(strip_tags($row->issue_text), 80))
            ->addColumn('fix_status_badge', function ($row) {
                $classes = [
                    'reviewed'   => 'info',
                    'assigned'   => 'primary',
                    'processing' => 'warning',
                    'solved'     => 'success',
                    'pending'    => 'secondary',
                ];
                $class = $classes[$row->issue_fix_status] ?? 'secondary';
                return '<span class="badge bg-' . $class . '-transparent">' . ucfirst($row->issue_fix_status) . '</span>';
            })
            ->addColumn('file_preview', function ($row) {
                $firstFile = $row->visualMerchandisingFiles->first();
                if (!$firstFile) {
                    return '<span class="inst-no-photos">No files</span>';
                }
                $ext   = strtolower(pathinfo((string) $firstFile->file_path, PATHINFO_EXTENSION));
                $count = $row->visualMerchandisingFiles->count();
                $extra = $count > 1 ? '<small class="text-muted d-block mt-1">+' . ($count - 1) . ' more</small>' : '';

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                    return '<div class="inst-photo-thumb"><img src="' . asset($firstFile->file_path) . '" alt="file"></div>' . $extra;
                }
                if (in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
                    return '<span class="badge bg-light text-dark"><i class="bi bi-camera-video me-1"></i>Video</span>' . $extra;
                }
                return '<span class="inst-no-photos">File</span>' . $extra;
            })
            ->addColumn('actions', function ($row) {
//                $nextStatusMap = [
//                    'pending'    => 'reviewed',
//                    'reviewed'   => 'assigned',
//                    'assigned'   => 'processing',
//                    'processing' => 'solved',
//                ];
//                $nextStatus = $nextStatusMap[$row->issue_fix_status] ?? null;
//                $changeBtn  = $nextStatus
//                    ? '<button class="btn-action change-vm-status" data-id="' . $row->id . '" data-fix-status="' . $nextStatus . '" title="Advance status"><i class="bi bi-arrow-repeat"></i></button>'
//                    : '';

                return '
                    <div class="d-flex gap-1">

                        <button class="btn-action btn-view-vm"   data-id="' . $row->id . '" title="View"><i class="bi bi-eye"></i></button>
                        <button class="btn-action btn-edit-vm"   data-id="' . $row->id . '" title="Edit"><i class="bi bi-pencil"></i></button>
                        <button class="btn-action text-danger btn-delete-vm" data-id="' . $row->id . '" data-name="' . e($row->asset?->name ?? 'VM Issue') . '" title="Delete"><i class="bi bi-trash"></i></button>
                    </div>';
            })
            ->rawColumns(['fix_status_badge', 'file_preview', 'actions'])
            ->filterColumn('store_name',    fn ($query, $keyword) => $query->whereHas('store', fn ($q) => $q->where('title', 'like', "%{$keyword}%")))
            ->filterColumn('asset_name',    fn ($query, $keyword) => $query->whereHas('asset', fn ($q) => $q->where('name', 'like', "%{$keyword}%")))
            ->filterColumn('issue_preview', fn ($query, $keyword) => $query->where('issue_text', 'like', "%{$keyword}%"));
    }

    public function query(): Builder
    {
        return VisualMerchandising::query()
            ->with([
                'store:id,title,code',
                'asset:id,name,asset_code,store_id,is_common_asset,asset_type_id',
                'asset.assetType:id,name',
                'visualMerchandisingFiles' => fn ($q) => $q->latest('id'),
            ])
            ->where('creator_id', CustomHelper::loggedUser()->id)
            ->latest();
    }

    protected function filename(): string
    {
        return 'VmIssues_' . date('YmdHis');
    }
}
