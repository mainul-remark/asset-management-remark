<?php

namespace App\Http\Controllers\Backend\Asset;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VisualMerchandisingFileController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('visual-merchandising.index');
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('visual-merchandising.index');
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Manage visual merchandising files from the visual merchandising form.',
        ], 405);
    }

    public function show(string $id): RedirectResponse
    {
        return redirect()->route('visual-merchandising.index');
    }

    public function edit(string $id): RedirectResponse
    {
        return redirect()->route('visual-merchandising.index');
    }

    public function update(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Manage visual merchandising files from the visual merchandising form.',
        ], 405);
    }

    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'message' => 'Manage visual merchandising files from the visual merchandising form.',
        ], 405);
    }
}
