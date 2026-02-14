<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use App\Models\AssetType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AssetTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.asset-management.asset-type', [
            'assetTypes' => AssetType::latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        if ($request->hasFile('default_image')) {
            $validated['default_image'] = $this->storeImage($request->file('default_image'));
        }

        $assetType = AssetType::create($validated);

        return response()->json([
            'message' => 'Asset type created successfully.',
            'data' => $assetType,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $assetType = AssetType::findOrFail($id);

        return response()->json($assetType);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $assetType = AssetType::findOrFail($id);

        return response()->json($assetType);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $assetType = AssetType::findOrFail($id);
        $validated = $this->validateData($request, $assetType->id);

        if ($request->hasFile('default_image')) {
            if ($assetType->default_image && File::exists(public_path($assetType->default_image))) {
                File::delete(public_path($assetType->default_image));
            }
            $validated['default_image'] = $this->storeImage($request->file('default_image'));
        }

        $assetType->update($validated);

        return response()->json([
            'message' => 'Asset type updated successfully.',
            'data' => $assetType,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assetType = AssetType::findOrFail($id);
        if ($assetType->default_image && File::exists(public_path($assetType->default_image))) {
            File::delete(public_path($assetType->default_image));
        }
        $assetType->delete();

        return response()->json([
            'message' => 'Asset type deleted successfully.',
        ]);
    }

    private function validateData(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'default_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'depth' => ['nullable', 'numeric', 'min:0'],
            'dimension_unit_name' => ['nullable', 'in:px,in,ft,cm,mm,m,yd'],
            'default_price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:0,1'],
            'is_digital' => ['nullable', 'in:0,1'],
            'total_self' => ['nullable', 'integer', 'min:0'],
            'has_kv_space' => ['nullable', 'in:0,1'],
        ]);
    }

    private function storeImage($file): string
    {
        $dir = public_path('uploads/asset-types');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }
        $name = 'asset-type-' . time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $name);

        return 'uploads/asset-types/' . $name;
    }
}
