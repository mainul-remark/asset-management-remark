<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\KV\BrandRequest;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class BrandController extends Controller
{
    public function index()
    {
        return view('backend.kv.brands', ['brands' => Brand::latest()->get()]);
    }

    public function store(BrandRequest $request): JsonResponse
    {
        try {
            $brand = DB::transaction(function () use ($request) {
                return Brand::updateOrCreateBrand($request);
            });

            return response()->json([
                'success' => true,
                'message' => 'Brand created successfully.',
                'data' => $brand->only(['id', 'name', 'code', 'description', 'logo', 'status', 'is_common']),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
//                'message' => 'Failed to create brand.',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

    public function show(Brand $brand): JsonResponse
    {
        return response()->json($brand);
    }

    public function edit(Brand $brand): JsonResponse
    {
        return response()->json($brand);
    }

    public function update(BrandRequest $request, Brand $brand): JsonResponse
    {
        try {
            $brand = DB::transaction(function () use ($request, $brand) {
                return Brand::updateOrCreateBrand($request, $brand);
            });

            return response()->json([
                'success' => true,
                'message' => 'Brand updated successfully.',
                'data' => $brand->only(['id', 'name', 'code', 'description', 'logo', 'status', 'is_common']),
            ]);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update brand.',
            ], 500);
        }
    }

    public function destroy(Brand $brand): JsonResponse
    {
        try {
            $brand->delete();

            return response()->json(['success' => true, 'message' => 'Brand deleted successfully.']);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete brand.',
            ], 500);
        }
    }
}
