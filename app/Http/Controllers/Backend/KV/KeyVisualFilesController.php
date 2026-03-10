<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\KV\KeyVisualFileRequest;
use App\Models\KeyVisual;
use App\Models\KeyVisualFiles;
use App\Models\KeyVisualSize;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KeyVisualFilesController extends Controller
{
    public function index(Request $request)
    {
        $keyVisuals = KeyVisual::query()
            ->select('id', 'name', 'unique_code')
            ->orderBy('name')
            ->get();

        $selectedKeyVisual = null;
        $requestedKeyVisualId = $request->integer('kv');

        if ($request->filled('kv')) {
            $selectedKeyVisual = $keyVisuals->firstWhere('id', $requestedKeyVisualId);
        }

        $kvFilesQuery = KeyVisualFiles::with([
            'keyVisual:id,name,unique_code',
            'keyVisualSize:id,name,width,height,unit_name',
        ])->latest();

        if ($selectedKeyVisual !== null) {
            $kvFilesQuery->where('key_visual_id', $selectedKeyVisual->id);
        }

        return view('backend.kv.kv-files', [
            'kvFiles' => $kvFilesQuery->get(),
            'keyVisuals' => $keyVisuals,
            'keyVisualSizes' => KeyVisualSize::query()
                ->select('id', 'name', 'width', 'height', 'unit_name')
                ->orderBy('name')
                ->get(),
            'selectedKeyVisualId' => $selectedKeyVisual?->id,
        ]);
    }

    public function create()
    {
        return redirect()->route('key-visual-files.index');
    }

    public function store(KeyVisualFileRequest $request)
    {
        $kvFile = KeyVisualFiles::create($this->preparePayload($request));

        return response()->json([
            'success' => true,
            'message' => 'Key visual file created successfully.',
            'data' => $kvFile->load([
                'keyVisual:id,name,unique_code',
                'keyVisualSize:id,name,width,height,unit_name',
            ]),
        ]);
    }

    public function show(string $id)
    {
        $kvFile = KeyVisualFiles::with([
            'keyVisual:id,name,unique_code',
            'keyVisualSize:id,name,width,height,unit_name',
        ])->findOrFail($id);

        return response()->json($kvFile);
    }

    public function edit(string $id)
    {
        $kvFile = KeyVisualFiles::findOrFail($id);

        return response()->json($kvFile);
    }

    public function update(KeyVisualFileRequest $request, string $id)
    {
        $kvFile = KeyVisualFiles::findOrFail($id);
        $kvFile->update($this->preparePayload($request, $kvFile));

        return response()->json([
            'success' => true,
            'message' => 'Key visual file updated successfully.',
            'data' => $kvFile->fresh()->load([
                'keyVisual:id,name,unique_code',
                'keyVisualSize:id,name,width,height,unit_name',
            ]),
        ]);
    }

    public function destroy(string $id)
    {
        $kvFile = KeyVisualFiles::findOrFail($id);

        if ($kvFile->kv_file && File::exists(public_path($kvFile->kv_file))) {
            File::delete(public_path($kvFile->kv_file));
        }

        $kvFile->delete();

        return response()->json([
            'success' => true,
            'message' => 'Key visual file deleted successfully.',
        ]);
    }

    private function preparePayload(KeyVisualFileRequest $request, ?KeyVisualFiles $kvFile = null): array
    {
        $validated = $request->validated();
        unset($validated['kv_file_upload'], $validated['media_width'], $validated['media_height']);

        $resolvedSizeId = $this->resolveKeyVisualSizeId($request, $validated, $kvFile);

        if ($resolvedSizeId !== null) {
            $validated['key_visual_size_id'] = $resolvedSizeId;
        }

        if ((!isset($validated['aspect_ratio']) || $validated['aspect_ratio'] === null || $validated['aspect_ratio'] === '')
            && isset($validated['key_visual_size_id'])) {
            $size = KeyVisualSize::query()
                ->select('width', 'height')
                ->find($validated['key_visual_size_id']);

            if ($size && (float) $size->height > 0) {
                $validated['aspect_ratio'] = round(((float) $size->width) / ((float) $size->height), 4);
            }
        }

        if (!isset($validated['aspect_ratio']) || $validated['aspect_ratio'] === null || $validated['aspect_ratio'] === '') {
            $validated['aspect_ratio'] = 0;
        }

        $validated['status'] = (int) $validated['status'];

        if ($request->hasFile('kv_file_upload')) {
            $file = $request->file('kv_file_upload');
            $directory = 'backend/assets/uploaded-files/key-visual-files';
            $absoluteDirectory = public_path($directory);
            $fileSizeInKb = (int) round(((int) $file->getSize()) / 1024);
            $fileMimeType = (string) ($file->getClientMimeType() ?? $validated['file_type'] ?? '');

            if (!File::isDirectory($absoluteDirectory)) {
                File::makeDirectory($absoluteDirectory, 0777, true, true);
            }

            if ($kvFile?->kv_file && File::exists(public_path($kvFile->kv_file))) {
                File::delete(public_path($kvFile->kv_file));
            }

            $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeBaseName = Str::slug($baseName) ?: 'kv-file';
            $fileName = 'key-visual-file-' . $safeBaseName . '_' . random_int(1000, 99999) . '.' . $file->getClientOriginalExtension();
            $file->move($absoluteDirectory, $fileName);

            $validated['kv_file'] = $directory . '/' . $fileName;
            $validated['kv_size'] = $fileSizeInKb;
            $validated['file_type'] = $fileMimeType;
        } elseif ($kvFile) {
            $validated['kv_file'] = $kvFile->kv_file;
        }

        if (!isset($validated['key_visual_size_id']) || $validated['key_visual_size_id'] === null || $validated['key_visual_size_id'] === '') {
            throw ValidationException::withMessages([
                'key_visual_size_id' => 'Key visual size could not be determined from the uploaded file. Please select one manually or upload a file with readable dimensions.',
            ]);
        }

        return $validated;
    }

    private function resolveKeyVisualSizeId(KeyVisualFileRequest $request, array $validated, ?KeyVisualFiles $kvFile = null): ?int
    {
        if ($request->hasFile('kv_file_upload')) {
            $dimensions = $this->extractMediaDimensions($request);

            if ($dimensions !== null) {
                return (int) $this->findOrCreateKeyVisualSize($dimensions['width'], $dimensions['height'])->getKey();
            }
        }

        if (!empty($validated['key_visual_size_id'])) {
            return (int) $validated['key_visual_size_id'];
        }

        return $kvFile?->key_visual_size_id;
    }

    private function extractMediaDimensions(KeyVisualFileRequest $request): ?array
    {
        $width = (int) ($request->input('media_width') ?? 0);
        $height = (int) ($request->input('media_height') ?? 0);

        if ($width > 0 && $height > 0) {
            return [
                'width' => $width,
                'height' => $height,
            ];
        }

        $file = $request->file('kv_file_upload');

        if (! $file instanceof UploadedFile || ! $this->isImageUpload($file)) {
            return null;
        }

        $realPath = $file->getRealPath();
        $imageSize = $realPath ? @getimagesize($realPath) : false;

        if (!is_array($imageSize) || empty($imageSize[0]) || empty($imageSize[1])) {
            return null;
        }

        return [
            'width' => (int) $imageSize[0],
            'height' => (int) $imageSize[1],
        ];
    }

    private function findOrCreateKeyVisualSize(int $width, int $height): KeyVisualSize
    {
        return KeyVisualSize::query()->firstOrCreate(
            [
                'width' => $width,
                'height' => $height,
                'unit_name' => 'px',
            ],
            [
                'name' => $width . ' x ' . $height,
                'status' => 1,
            ]
        );
    }

    private function isImageUpload(UploadedFile $file): bool
    {
        $mimeType = (string) ($file->getClientMimeType() ?? $file->getMimeType() ?? '');
        $extension = strtolower((string) $file->getClientOriginalExtension());

        return str_starts_with($mimeType, 'image/')
            || in_array($extension, ['jpeg', 'jpg', 'png', 'gif', 'svg', 'webp'], true);
    }
}
