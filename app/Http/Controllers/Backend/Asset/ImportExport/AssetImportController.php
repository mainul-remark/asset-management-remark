<?php

namespace App\Http\Controllers\Backend\Asset\ImportExport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\Backend\Asset\AssetImportRequest;
use App\Imports\Asset\AssetsImport;

class AssetImportController extends Controller
{
    /**
     * Handle the Excel import request.
     *
     * Flow:
     *  1. Validate the uploaded file (mime + size).
     *  2. Run the importer — it validates every row and bulk-inserts valid ones.
     *  3a. If there are row-level failures → return a downloadable error report.
     *  3b. Otherwise → return a success JSON response.
     */
    public function import(AssetImportRequest $request): JsonResponse
    {
        $import = new AssetsImport();

        Excel::import($import, $request->file('file'));

        if ($import->hasFailures()) {
            // Build a structured error payload grouped by row
            $failures = $import->getFailures();

            return response()->json([
                'success'        => false,
                'message'        => 'Import failed. Please fix the listed rows and re-upload.',
                'imported_count' => $import->getImportedCount(),
                'error_count'    => count($failures),
                'errors'         => $this->formatFailures($failures),
            ], 422);
        }

        return response()->json([
            'success'        => true,
            'message'        => "Import successful. {$import->getImportedCount()} asset(s) imported.",
            'imported_count' => $import->getImportedCount(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Format raw failure records into an API-friendly structure.
     *
     * @param  array<int, array{row: int, errors: string[]}>  $failures
     * @return array<int, array{row: int, errors: string[]}>
     */
    private function formatFailures(array $failures): array
    {
        return array_map(fn ($f) => [
            'row'    => $f['row'],
            'errors' => $f['errors'],
        ], $failures);
    }
}
