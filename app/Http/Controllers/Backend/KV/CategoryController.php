<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $parentId = $request->query('category');
        $parent = $parentId ? Category::findOrFail($parentId) : null;

        // Build breadcrumb trail
        $breadcrumbs = collect();
        if ($parent) {
            $ancestor = $parent;
            while ($ancestor) {
                $breadcrumbs->prepend($ancestor);
                $ancestor = $ancestor->parent;
            }
        }

        $categories = Category::where('category_id', $parentId)
            ->withCount('children')
            ->latest()
            ->get();

        // For parent dropdown in modal: show categories that are valid parents (exclude current level's children context)
        $allCategories = Category::latest()->get();

        return view('backend.kv.categories', compact('categories', 'allCategories', 'parent', 'breadcrumbs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'name'        => 'required|string|max:255|unique:categories,name',
            'code'        => 'required|string|min:2|max:3|alpha|unique:categories,code',
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:0,1',
        ], $this->validationMessages());

        try {
            $category = DB::transaction(function () use ($request) {
                return Category::updateOrCreateCategory($request);
            });

            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'data' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'code' => $category->code,
                    'category_id' => $category->category_id,
                ],
            ]);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $category = Category::with('parent')->findOrFail($id);
        return response()->json($category);
    }

    public function edit(string $id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'category_id' => ['nullable', 'exists:categories,id', function ($attribute, $value, $fail) use ($id) {
                if ($value == $id) {
                    $fail('A category cannot be its own parent.');
                }
            }],
            'name'        => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category->id)],
            'code'        => ['required', 'string', 'min:2', 'max:3', 'alpha', Rule::unique('categories', 'code')->ignore($category->id)],
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:0,1',
        ], $this->validationMessages());

        try {
            DB::transaction(function () use ($request, $category) {
                Category::updateOrCreateCategory($request, $category);
            });
            return response()->json(['success' => true, 'message' => 'Category updated successfully.']);
        } catch (\Exception $exception) {
            return response()->json(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted successfully.']);
    }

    private function validationMessages(): array
    {
        return [
            'name.required'    => 'The category name is required.',
            'name.max'         => 'The category name cannot exceed 255 characters.',
            'name.unique'      => 'This category name is already taken.',
            'code.required'    => 'The category code is required.',
            'code.min'         => 'The category code must be at least 2 characters.',
            'code.max'         => 'The category code cannot exceed 3 characters.',
            'code.alpha'       => 'The category code must contain only letters.',
            'code.unique'      => 'This category code is already in use.',
            'description.max'  => 'The description cannot exceed 1000 characters.',
            'status.required'  => 'The category status is required.',
            'status.in'        => 'The status must be either active or inactive.',
            'category_id.exists' => 'The selected parent category does not exist.',
        ];
    }
}
