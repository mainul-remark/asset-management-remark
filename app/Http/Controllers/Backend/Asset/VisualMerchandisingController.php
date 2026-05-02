<?php

namespace App\Http\Controllers\Backend\Asset;

use App\DataTables\VmIssuesDataTable;
use App\Exports\VmIssues\VmIssuesExport;
use App\HelperFiles\HelperFile;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Asset\VisualMerchandisingRequest;
use App\Models\Asset;
use App\Models\Store;
use App\Models\VisualMerchandising;
use App\Models\VmIssueFix;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class VisualMerchandisingController extends Controller
{
    public function index()
    {
        return view('backend.asset-management.vm-admin', [
            'visualMerchandisings' => VisualMerchandising::query()
                ->with([
                    'store:id,title,code',
                    'asset:id,name,asset_code,store_id,is_common_asset,asset_type_id',
                    'asset.assetType:id,name',
                    'creator:id,name,email',
                    'visualMerchandisingFiles' => fn ($query) => $query->latest('id'),
                ])
                ->latest()
                ->get(),
            'stores' => Store::query()
                ->whereNull('deleted_at')
                ->orderBy('title')
                ->get(['id', 'title', 'code', 'status']),
            'assets' => Asset::query()
                ->with([
                    'store:id,title,code',
                    'assetType:id,name',
                    'assignAssetToStores:id,asset_id,store_id',
                ])
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'asset_code', 'store_id', 'is_common_asset', 'status', 'asset_type_id']),
            'issueFixStatuses' => VisualMerchandisingRequest::ISSUE_FIX_STATUSES,
        ]);
    }

    public function create()
    {
        return redirect()->route('visual-merchandising.index');
    }

    public function store(VisualMerchandisingRequest $request)
    {
        $visualMerchandising = DB::transaction( function () use ($request) {
            return VisualMerchandising::updateOrCreateVisualMerchandising($request);
//            if ($visualMerchandising) {
//                VmIssueFix::createOrUpdateVmIssueFix($request, $visualMerchandising);
//            }
        });

        return response()->json([
            'message' => 'Visual merchandising issue created successfully.',
            'data' => $this->serializeVisualMerchandising($visualMerchandising),
        ]);
    }

    public function show(string $id): JsonResponse
    {
        $visualMerchandising = VisualMerchandising::findOrFail($id);
        $visualMerchandising->load([
            'store:id,title,code',
            'asset:id,name,asset_code,store_id,is_common_asset,asset_type_id',
            'asset.assetType:id,name',
            'creator:id,name,email',
            'visualMerchandisingFiles' => fn ($query) => $query->latest('id'),
        ]);

        return response()->json($this->serializeVisualMerchandising($visualMerchandising));
    }

    public function edit(string $id): JsonResponse
    {
        $visualMerchandising = VisualMerchandising::findOrFail($id);
        if ($visualMerchandising->issue_fix_status == 'pending') {
            $visualMerchandising['can_edit'] = true;
        }
        $visualMerchandising->load([
            'store:id,title,code',
            'asset:id,name,asset_code,store_id,is_common_asset,asset_type_id',
            'asset.assetType:id,name',
            'creator:id,name,email',
            'visualMerchandisingFiles' => fn ($query) => $query->latest('id'),
        ]);

        return response()->json($this->serializeVisualMerchandising($visualMerchandising));
    }

    public function update(VisualMerchandisingRequest $request, string $id): JsonResponse
    {
        $visualMerchandising = VisualMerchandising::findOrFail($id);
        $visualMerchandising = DB::transaction(
            fn () => VisualMerchandising::updateOrCreateVisualMerchandising($request, $visualMerchandising)
        );

        return response()->json([
            'message' => 'Visual merchandising issue updated successfully.',
            'data' => $this->serializeVisualMerchandising($visualMerchandising),
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $visualMerchandising = VisualMerchandising::findOrFail($id);
        DB::transaction(fn () => $visualMerchandising->delete());

        return response()->json([
            'message' => 'Visual merchandising issue deleted successfully.',
        ]);
    }

    private function serializeVisualMerchandising(VisualMerchandising $visualMerchandising): array
    {
        return [
            'id' => $visualMerchandising->id,
            'store_id' => $visualMerchandising->store_id,
            'asset_id' => $visualMerchandising->asset_id,
            'can_edit' => $visualMerchandising->can_edit ?? false,
            'creator_id' => $visualMerchandising->creator_id,
            'issue_text' => $visualMerchandising->issue_text,
            'issue_fix_status' => $visualMerchandising->issue_fix_status,
            'status' => (int) $visualMerchandising->status,
            'created_at' => $visualMerchandising->created_at?->toISOString(),
            'updated_at' => $visualMerchandising->updated_at?->toISOString(),
            'store' => $visualMerchandising->store ? [
                'id' => $visualMerchandising->store->id,
                'title' => $visualMerchandising->store->title,
                'code' => $visualMerchandising->store->code,
            ] : null,
            'creator' => $visualMerchandising->creator ? [
                'id' => $visualMerchandising->creator->id,
                'name' => $visualMerchandising->creator->name,
                'email' => $visualMerchandising->creator->email,
            ] : null,
            'asset' => $visualMerchandising->asset ? [
                'id' => $visualMerchandising->asset->id,
                'name' => $visualMerchandising->asset->name,
                'asset_code' => $visualMerchandising->asset->asset_code,
                'store_id' => $visualMerchandising->asset->store_id,
                'is_common_asset' => (int) $visualMerchandising->asset->is_common_asset,
                'asset_type' => $visualMerchandising->asset->assetType ? [
                    'id' => $visualMerchandising->asset->assetType->id,
                    'name' => $visualMerchandising->asset->assetType->name,
                ] : null,
            ] : null,
            'visual_merchandising_files' => $visualMerchandising->visualMerchandisingFiles
                ->map(function ($file) {
                    return [
                        'id' => $file->id,
                        'file_path' => $file->file_path,
                        'file_url' => asset($file->file_path),
                        'file_type' => $file->file_type,
                        'file_name' => basename((string) $file->file_path),
                        'preview_type' => $this->resolvePreviewType((string) $file->file_type, (string) $file->file_path),
                    ];
                })
                ->values()
                ->all(),
        ];
    }

    private function resolvePreviewType(string $fileType, string $filePath): string
    {
        $normalizedType = strtolower($fileType);
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (str_starts_with($normalizedType, 'image/') || in_array($extension, ['jpeg', 'jpg', 'png', 'gif', 'svg', 'webp'], true)) {
            return 'image';
        }

        if (str_starts_with($normalizedType, 'video/') || in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm'], true)) {
            return 'video';
        }

        return 'file';
    }

    public function userWiseVmIssues(Request $request)
    {
        return view('backend.asset-management.vm-issues-theme', [
            'stores' => Store::query()
                ->whereNull('deleted_at')
                ->orderBy('title')
                ->get(['id', 'title', 'code', 'status']),
            'assets' => Asset::query()
                ->with([
                    'store:id,title,code',
                    'assetType:id,name',
                    'assignAssetToStores:id,asset_id,store_id',
                ])
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'asset_code', 'store_id', 'is_common_asset', 'status', 'asset_type_id']),
            'issueFixStatuses' => VisualMerchandisingRequest::ISSUE_FIX_STATUSES,
            'permissions' => [
                'canView'         => allowed([self::class, 'show']),
                'canCreate'       => allowed([self::class, 'store']),
                'canEdit'         => allowed([self::class, 'edit']),
                'canDelete'       => allowed([self::class, 'destroy']),
                'canExport'       => allowed([self::class, 'exportVmIssues']),
                'canChangeStatus' => allowed([self::class, 'changeVmIssueStatus']),
            ],
        ]);
    }

    public function exportVmIssues(Request $request): JsonResponse
    {
        $filename = 'vm-issues-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        $key = HelperFile::exportExelOnQueue(
            new VmIssuesExport(
                creatorId: CustomHelper::loggedUser()->id,
                fixStatus: $request->filled('fix_status') ? $request->fix_status : null,
                storeId:   $request->filled('store_id')   ? (int) $request->store_id : null,
            ),
            $filename
        );

        return response()->json(['key' => $key]);
    }

    public function exportVmIssuesStatus(string $key): JsonResponse
    {
        return HelperFile::exportStatus($key, 'vm.vm-issues.export.download');
    }

    public function exportVmIssuesDownload(string $key): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return HelperFile::exportDownload($key);
    }

    public function vmIssuesDatatable(Request $request, VmIssuesDataTable $dataTable)
    {
        if ($request->filled('fix_status')) {
            $dataTable->addScope(new \App\DataTables\Scopes\FixStatusScope($request->fix_status));
        }
        if ($request->filled('store_id')) {
            $dataTable->addScope(new \App\DataTables\Scopes\StoreScope($request->store_id));
        }

        return $dataTable->ajax();
    }

    public function changeVmIssueStatus(Request $request, VisualMerchandising $visualMerchandising, $issueStatus = 'pending')
    {
        try {
            $visualMerchandising->update(['issue_fix_status' => $issueStatus]);
            $visualMerchandising->load([
                'store:id,title,code',
                'asset:id,name,asset_code,store_id,is_common_asset,asset_type_id',
                'asset.assetType:id,name',
                'visualMerchandisingFiles' => fn ($q) => $q->latest('id'),
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Issue status changed successfully.',
                'vm'      => $this->serializeVisualMerchandising($visualMerchandising),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
