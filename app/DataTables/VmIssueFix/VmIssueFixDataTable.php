<?php

namespace App\DataTables\VmIssueFix;

use App\Models\VisualMerchandising;
use App\Services\StatusPermission\StatusPermissionService;
use Illuminate\Database\Eloquent\Builder;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Services\DataTable;

class VmIssueFixDataTable extends DataTable
{
    private array $permissions = [];

    public function withPermissions(array $permissions): static
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function dataTable($query): DataTableAbstract
    {
        return datatables()
            ->eloquent($query)
            ->addIndexColumn()
            ->addColumn('store_asset', function ($row) {
                $store = e($row->store?->title ?? '');
                $asset = e($row->asset?->name ?? '');
                return '<div class="fw-semibold">' . $store . '</div>'
                     . '<small class="text-muted">' . $asset . '</small>';
            })
            ->addColumn('issue_preview', function ($row) {
                return '<div style="max-width:200px;white-space:normal">'
                     . e(\Illuminate\Support\Str::limit(strip_tags($row->issue_text ?? ''), 80))
                     . '</div>';
            })
            ->addColumn('issue_photos', function ($row) {
                $files = $row->visualMerchandisingFiles;
                if ($files->isEmpty()) {
                    return '<span class="text-muted">—</span>';
                }
                $html = '';
                foreach ($files->take(3) as $file) {
                    $html .= '<img src="' . asset($file->file_path) . '" style="height:50px;width:auto;border-radius:4px;margin-right:3px;object-fit:cover">';
                }
                if ($files->count() > 3) {
                    $html .= '<small class="text-muted ms-1">+' . ($files->count() - 3) . ' more</small>';
                }
                return $html;
            })
            ->addColumn('assigned_to_name', fn ($row) => e($row->assignedTo?->name ?? '—'))
            ->addColumn('assigned_by_name', fn ($row) => e($row->assignedBy?->name ?? '—'))
            ->addColumn('fix_photos', function ($row) {
                if (!$row->fix_proof) {
                    return '<span class="text-muted">—</span>';
                }
                $paths = json_decode($row->fix_proof, true) ?? [];
                if (empty($paths)) {
                    return '<span class="text-muted">—</span>';
                }
                $html = '';
                foreach (array_slice($paths, 0, 3) as $path) {
                    $html .= '<img src="' . asset($path) . '" style="height:50px;width:auto;border-radius:4px;margin-right:3px;object-fit:cover">';
                }
                if (count($paths) > 3) {
                    $html .= '<small class="text-muted ms-1">+' . (count($paths) - 3) . ' more</small>';
                }
                return $html;
            })
            ->addColumn('status_select', function ($row) {
                // build allowed options once per row using StatusPermissionService
                $allowedSlugs = app(StatusPermissionService::class)
                    ->allowedStatuses()
                    ->pluck('slug')
                    ->flip(); // slug => index for fast lookup

                if (!($this->permissions['canChangeStatus'] ?? false) || $allowedSlugs->isEmpty()) {
                    $labels = [
                        'pending'    => 'Pending',
                        'planned'    => 'Planned',
                        'assigned'   => 'Assigned',
                        'processing' => 'Processing',
                        'solved'     => 'Solved',
                    ];
                    return '<span class="badge bg-secondary-transparent">' . e($labels[$row->issue_fix_status] ?? $row->issue_fix_status) . '</span>';
                }
                // all possible options — filtered to what this user may set
                $allOptions = [
                    'pending'    => 'Pending',
                    'planned'    => 'Planned',
                    'assigned'   => 'Assigned',
                    'processing' => 'Processing',
                    'solved'     => 'Solved',
                ];
                // keep current status in list even if user can't set it (shows where it currently is)
                $options = array_filter(
                    $allOptions,
                    fn($val) => $allowedSlugs->has($val) || $val === $row->issue_fix_status,
                    ARRAY_FILTER_USE_KEY
                );

                $html = '<select class="form-control form-control-sm change-status" data-vm-id="' . $row->id . '" style="min-width:120px">';
                foreach ($options as $val => $label) {
                    $selected  = $row->issue_fix_status === $val ? ' selected' : '';
                    $disabled  = $val === $row->issue_fix_status && !$allowedSlugs->has($val) ? ' disabled' : '';
                    $html .= '<option value="' . $val . '"' . $selected . $disabled . '>' . $label . '</option>';
                }
                $html .= '</select>';
                return $html;
            })
            ->addColumn('actions', function ($row) {
                $perms = $this->permissions;
                $buttons = '';
                if ($perms['canView'] ?? false) {
                    $buttons .= '<a href="" class="btn btn-sm btn-secondary view-vm" data-vm-id="' . $row->id . '" title="View"><i class="ri-eye-line"></i></a>';
                }
                if (($perms['canAssignUser'] ?? false) && $row->issue_fix_status !== 'solved') {
                    $buttons .= '<a href="" class="btn btn-sm btn-secondary assign-user" data-vm-id="' . $row->id . '" title="Assign"><i class="ri-user-line"></i></a>';
                }
                if ($perms['canUploadProof'] ?? false) {
                    $buttons .= '<a href="" class="btn btn-sm btn-secondary upload-proof" data-vm-id="' . $row->id . '" title="Upload Proof"><i class="ri-file-2-line"></i></a>';
                }
                return $buttons ? '<div class="d-flex gap-1">' . $buttons . '</div>' : '—';
            })
            ->rawColumns(['store_asset', 'issue_preview', 'issue_photos', 'fix_photos', 'status_select', 'actions'])
            ->filterColumn('store_asset', function ($q, $kw) {
                $q->where(function ($sub) use ($kw) {
                    $sub->whereHas('store', fn ($s) => $s->where('title', 'like', "%{$kw}%"))
                        ->orWhereHas('asset', fn ($a) => $a->where('name', 'like', "%{$kw}%"));
                });
            })
            ->filterColumn('assigned_to_name', fn ($q, $kw) =>
                $q->whereHas('assignedTo', fn ($u) => $u->where('name', 'like', "%{$kw}%"))
            );
    }

    public function query(): Builder
    {
        return VisualMerchandising::query()
            ->with([
                'store:id,title',
                'asset:id,name',
                'assignedTo:id,name',
                'assignedBy:id,name',
                'visualMerchandisingFiles',
            ])
            ->latest();
    }

    protected function filename(): string
    {
        return 'VmIssueFix_' . date('YmdHis');
    }
}
