<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\KV\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        return view('backend.kv.brands', ['brands' => Brand::latest()->get()]);
    }

    public function store(BrandRequest $request)
    {
        try {
            $brand = DB::transaction(function () use ($request) {
                return Brand::updateOrCreateBrand($request);
            });

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully.',
                'data' => [
                    'id' => $brand->id,
                    'name' => $brand->name,
                    'code' => $brand->code,
                ],
            ]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function show( string $id)
    {
        $brand = Brand::findOrFail($id);
        return response()->json($brand);
    }

    public function edit(Brand $brand)
    {
        return view('backend.kv.partials.edit-brand', ['brand' => $brand, 'isShow' => true])->render();
        $brand = Brand::findOrFail($id);
        return response()->json($brand);
    }

    public function update(Request $request, string $id)
    {
        $brand = Brand::findOrFail($id);

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255', Rule::unique('brands', 'name')->ignore($brand->id)],
            'code'        => ['required', 'string', 'min:2', 'max:3', 'alpha', Rule::unique('brands', 'code')->ignore($brand->id)],
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:0,1',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            // Name validation messages
            'name.required'    => 'The brand name is required.',
            'name.string'      => 'The brand name must be a valid text.',
            'name.max'         => 'The brand name cannot exceed 255 characters.',
            'name.unique'      => 'This brand name is already taken.',

            // Code validation messages
            'code.required'    => 'The brand code is required.',
            'code.string'      => 'The brand code must be a valid text.',
            'code.min'         => 'The brand code must be at least 2 characters.',
            'code.max'         => 'The brand code cannot exceed 3 characters.',
            'code.alpha'       => 'The brand code must contain only letters.',
            'code.unique'      => 'This brand code is already in use.',

            // Description validation messages
            'description.string' => 'The description must be a valid text.',
            'description.max'    => 'The description cannot exceed 1000 characters.',

            // Status validation messages
            'status.required'  => 'The brand status is required.',
            'status.in'        => 'The status must be either active or inactive.',

            // Logo validation messages
            'logo.image'       => 'The logo must be an image file.',
            'logo.mimes'       => 'The logo must be a file of type: jpeg, png, jpg, gif, svg, or webp.',
            'logo.max'         => 'The logo size cannot exceed 2MB.',
        ]);
        try {
            DB::transaction(function () use ($request, $brand) {
                $brand = Brand::updateOrCreateBrand($request, $brand);
            });
            return response()->json(['success' => true, 'message' => 'Brand updated successfully.']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $brand = Brand::findOrFail($id);

        $brand->delete();

        return response()->json(['success' => true, 'message' => 'Brand deleted successfully.']);
    }
}
