<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\KV\BrandController;
use App\Http\Controllers\Backend\KV\CategoryController;
use App\Http\Requests\Backend\KV\KeyVisualRequest;
use App\Models\AssetType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\KeyVisual;
use App\Models\KeyVisualSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class KeyVisualController extends Controller
{
    public function index()
    {
        return view('backend.kv.kv-theme', [
            'keyVisuals'     => KeyVisual::with('assetType:id,name', 'brands:id,name,code', 'categories:id,name,code')->latest()->get(),
            'assetTypes'     => AssetType::orderBy('name')->get(['id', 'name']),
            'brands'         => Brand::orderBy('name')->get(['id', 'name', 'code']),
            'categories'     => Category::orderBy('name')->get(['id', 'name', 'code']),
            'keyVisualSizes' => KeyVisualSize::orderBy('name')->get(['id', 'name', 'width', 'height', 'unit_name']),
            'permissions'    => [
                'canCreate'         => allowed([self::class, 'store']),
                'canView'           => allowed([self::class, 'show']),
                'canEdit'           => allowed([self::class, 'edit']),
                'canDelete'         => allowed([self::class, 'destroy']),
                'canCreateBrand'    => allowed([BrandController::class, 'store']),
                'canEditBrand'      => allowed([BrandController::class, 'edit']),
                'canDeleteBrand'    => allowed([BrandController::class, 'destroy']),
                'canCreateCategory' => allowed([CategoryController::class, 'store']),
                'canEditCategory'   => allowed([CategoryController::class, 'edit']),
                'canDeleteCategory' => allowed([CategoryController::class, 'destroy']),
            ],
        ]);
    }
    public function old()
    {
        return view('backend.kv.kv', [
            'keyVisuals' => KeyVisual::with('assetType:id,name', 'brands:id,name,code', 'categories:id,name,code')->latest()->get(),
            'assetTypes' => AssetType::orderBy('name')->get(['id', 'name']),
            'brands'     => Brand::orderBy('name')->get(['id', 'name', 'code']),
            'categories' => Category::orderBy('name')->get(['id', 'name', 'code']),
        ]);
    }

    public function create()
    {
        return redirect()->route('key-visuals.index');
    }

    public function store(KeyVisualRequest $request)
    {
        try {
            $keyVisual = DB::transaction(function () use ($request) {
                $kv = KeyVisual::updateOrCreateKeyVisual($request);
                $kv->brands()->sync($this->filterIds($request->input('brand_ids')));
                $kv->categories()->sync($this->filterIds($request->input('category_ids')));
                return $kv;
            });

            return response()->json([
                'message' => 'Key visual created successfully.',
                'data'    => $keyVisual->load('assetType:id,name'),
            ]);
        } catch (\Throwable $e) {
            Log::error('KeyVisual store failed', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to save key visual. Please try again.'], 500);
        }
    }

    public function show(string $id)
    {
        return response()->json(
            KeyVisual::with('assetType:id,name')->findOrFail($id)
        );
    }

    public function edit(string $id)
    {
        $kv            = KeyVisual::with('brands:id,name,code', 'categories:id,name,code')->findOrFail($id);
        $firstBrand    = $kv->brands->first();
        $firstCategory = $kv->categories->first();

        return response()->json(array_merge($kv->toArray(), [
            'selected_brand_id'      => $firstBrand?->id,
            'selected_brand_code'    => $firstBrand    ? strtoupper($firstBrand->code)    : null,
            'selected_category_id'   => $firstCategory?->id,
            'selected_category_code' => $firstCategory ? strtoupper($firstCategory->code) : null,
        ]));
    }

    public function update(KeyVisualRequest $request, string $id)
    {
        $keyVisual = KeyVisual::findOrFail($id);

        try {
            $keyVisual = DB::transaction(function () use ($request, $keyVisual) {
                $kv = KeyVisual::updateOrCreateKeyVisual($request, $keyVisual);
                $kv->brands()->sync($this->filterIds($request->input('brand_ids')));
                $kv->categories()->sync($this->filterIds($request->input('category_ids')));
                return $kv;
            });

            return response()->json([
                'message' => 'Key visual updated successfully.',
                'data'    => $keyVisual->fresh()->load('assetType:id,name'),
            ]);
        } catch (\Throwable $e) {
            Log::error('KeyVisual update failed', ['id' => $id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to update key visual. Please try again.'], 500);
        }
    }

    public function destroy(string $id)
    {
        KeyVisual::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Key visual deleted successfully.',
        ]);
    }

    public function nextUniqueCode(Request $request)
    {
        $validated = $request->validate([
            'brand_code'    => ['required', 'string', 'max:50'],
            'category_code' => ['required', 'string', 'max:50'],
        ]);

        $prefix = strtoupper(trim($validated['brand_code'])) . '-' . strtoupper(trim($validated['category_code'])) . '-';

        $lastNumber = (int) KeyVisual::withTrashed()
            ->where('unique_code', 'like', $prefix . '%')
            ->whereRaw("SUBSTRING_INDEX(unique_code, '-', -1) REGEXP '^[0-9]+$'")
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(unique_code, '-', -1) AS UNSIGNED)) as last_number")
            ->value('last_number');

        $nextNumber = str_pad((string) ($lastNumber + 1), 3, '0', STR_PAD_LEFT);

        return response()->json([
            'unique_code' => $prefix . $nextNumber,
            'next_number' => $nextNumber,
        ]);
    }

    private function filterIds(mixed $ids): array
    {
        return collect((array) $ids)
            ->filter(fn($v) => is_numeric($v) && (int) $v > 0)
            ->map(fn($v) => (int) $v)
            ->values()
            ->toArray();
    }



}
