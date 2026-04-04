<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Asset\VisualMerchandisingRequest;
use App\Models\Asset;
use App\Models\Store;
use App\Models\VisualMerchandising;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

    public function store(VisualMerchandisingRequest $request): JsonResponse
    {
        $visualMerchandising = DB::transaction(
            fn () => VisualMerchandising::updateOrCreateVisualMerchandising($request)
        );

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
            'visualMerchandisingFiles' => fn ($query) => $query->latest('id'),
        ]);

        return response()->json($this->serializeVisualMerchandising($visualMerchandising));
    }

    public function edit(string $id): JsonResponse
    {
        $visualMerchandising = VisualMerchandising::findOrFail($id);
        $visualMerchandising->load([
            'store:id,title,code',
            'asset:id,name,asset_code,store_id,is_common_asset,asset_type_id',
            'asset.assetType:id,name',
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
}
