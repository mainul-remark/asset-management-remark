<?php

namespace App\Http\Controllers\Backend\KV;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\KV\KeyVisualSizeRequest;
use App\Models\KeyVisualSize;

class KeyVisualSizesController extends Controller
{
    public function index()
    {
        return view('backend.kv.kv-sizes', [
            'kvSizes'     => KeyVisualSize::latest()->get(),
            'permissions' => [
                'canCreate' => allowed('key-visual-sizes.store'),
                'canView'   => allowed('key-visual-sizes.show'),
                'canEdit'   => allowed('key-visual-sizes.edit'),
                'canDelete' => allowed('key-visual-sizes.destroy'),
            ],
        ]);
    }

    public function create()
    {
        return redirect()->route('key-visual-sizes.index');
    }

    public function store(KeyVisualSizeRequest $request)
    {
        $size = KeyVisualSize::create($this->validatedPayload($request));

        return response()->json([
            'success' => true,
            'message' => 'Key visual size created successfully.',
            'data' => $size,
        ]);
    }

    public function show(string $id)
    {
        $size = KeyVisualSize::findOrFail($id);

        return response()->json($size);
    }

    public function edit(string $id)
    {
        $size = KeyVisualSize::findOrFail($id);

        return response()->json($size);
    }

    public function update(KeyVisualSizeRequest $request, string $id)
    {
        $size = KeyVisualSize::findOrFail($id);
        $size->update($this->validatedPayload($request));

        return response()->json([
            'success' => true,
            'message' => 'Key visual size updated successfully.',
            'data' => $size->fresh(),
        ]);
    }

    public function destroy(string $id)
    {
        $size = KeyVisualSize::findOrFail($id);
        $size->delete();

        return response()->json([
            'success' => true,
            'message' => 'Key visual size deleted successfully.',
        ]);
    }

    private function validatedPayload(KeyVisualSizeRequest $request): array
    {
        $validated = $request->validated();
        $validated['status'] = (int) $validated['status'];

        return $validated;
    }
}
