<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\KV\KeyVisualFileRequest;
use App\Models\KeyVisual;
use App\Models\KeyVisualFiles;
use App\Models\KeyVisualSize;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class KeyVisualFilesController extends Controller
{
    public function index()
    {
        return view('backend.kv.kv-files', [
            'kvFiles' => KeyVisualFiles::with([
                'keyVisual:id,name,unique_code',
                'keyVisualSize:id,name,width,height,unit_name',
            ])->latest()->get(),
            'keyVisuals' => KeyVisual::query()
                ->select('id', 'name', 'unique_code')
                ->orderBy('name')
                ->get(),
            'keyVisualSizes' => KeyVisualSize::query()
                ->select('id', 'name', 'width', 'height', 'unit_name')
                ->orderBy('name')
                ->get(),
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
        unset($validated['kv_file_upload']);

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
            $validated['kv_size'] = (int) round(((int) $file->getSize()) / 1024);
            $validated['file_type'] = (string) ($file->getClientMimeType() ?? $validated['file_type'] ?? '');
        } elseif ($kvFile) {
            $validated['kv_file'] = $kvFile->kv_file;
        }

        return $validated;
    }
}
