<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.asset-management.assets', [
            'assets' => Asset::with(['assetType:id,name', 'store:id,title,code'])->latest()->get(),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name']),
            'stores' => Store::orderBy('title')->get(['id', 'title', 'code', 'slug']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->route('assets.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $validated['default_image'] = $this->storeFile(
            $request->file('default_image'),
            'uploads/assets/images',
            'asset-image'
        );

        if ($request->hasFile('planogram_pdf')) {
            $validated['planogram_pdf'] = $this->storeFile(
                $request->file('planogram_pdf'),
                'uploads/assets/planograms',
                'asset-planogram'
            );
        }

        $asset = Asset::create($validated);

        return response()->json([
            'message' => 'Asset created successfully.',
            'data' => $asset->load(['assetType:id,name', 'store:id,title,code']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $asset = Asset::with(['assetType:id,name', 'store:id,title,code'])->findOrFail($id);

        return response()->json($asset);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $asset = Asset::findOrFail($id);

        return response()->json($asset);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $asset = Asset::findOrFail($id);
        $validated = $this->validateData($request, $asset);

        if ($request->hasFile('default_image')) {
            $this->deleteFile($asset->default_image);
            $validated['default_image'] = $this->storeFile(
                $request->file('default_image'),
                'uploads/assets/images',
                'asset-image'
            );
        }

        if ($request->hasFile('planogram_pdf')) {
            $this->deleteFile($asset->planogram_pdf);
            $validated['planogram_pdf'] = $this->storeFile(
                $request->file('planogram_pdf'),
                'uploads/assets/planograms',
                'asset-planogram'
            );
        }

        $asset->update($validated);

        return response()->json([
            'message' => 'Asset updated successfully.',
            'data' => $asset->fresh()->load(['assetType:id,name', 'store:id,title,code']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $asset = Asset::findOrFail($id);

        $this->deleteFile($asset->default_image);
        $this->deleteFile($asset->planogram_pdf);

        $asset->delete();

        return response()->json([
            'message' => 'Asset deleted successfully.',
        ]);
    }

    private function validateData(Request $request, ?Asset $asset = null): array
    {
        $validated = $request->validate([
            'asset_type_id' => ['required', 'exists:asset_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'default_image' => [$asset ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'has_kv_slot' => ['nullable', 'in:0,1'],
            'minimum_fee' => ['nullable', 'numeric', 'min:0'],
            'asset_price' => ['nullable', 'numeric', 'min:0'],
            'is_common_asset' => ['nullable', 'in:0,1'],
            'planogram_pdf' => ['nullable', 'mimes:pdf', 'max:10240'],
            'status' => ['nullable', 'in:0,1'],
            'has_self' => ['nullable', 'in:0,1'],
            'total_self' => ['nullable', 'integer', 'min:0', 'max:127'],
        ]);

        $validated['has_kv_slot'] = $request->boolean('has_kv_slot') ? 1 : 0;
        $validated['is_common_asset'] = $request->boolean('is_common_asset') ? 1 : 0;
        $validated['status'] = $request->boolean('status') ? 1 : 0;
        $validated['has_self'] = $request->boolean('has_self') ? 1 : 0;

        if ($validated['is_common_asset'] === 1) {
            $validated['store_id'] = null;
        }

        if ($validated['has_self'] === 0) {
            $validated['total_self'] = null;
        }

        return $validated;
    }

    private function storeFile(UploadedFile $file, string $directory, string $prefix): string
    {
        $absoluteDirectory = public_path($directory);

        if (!File::exists($absoluteDirectory)) {
            File::makeDirectory($absoluteDirectory, 0755, true);
        }

        $fileName = $prefix . '-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($absoluteDirectory, $fileName);

        return trim($directory, '/') . '/' . $fileName;
    }

    private function deleteFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        $absolutePath = public_path($path);
        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
