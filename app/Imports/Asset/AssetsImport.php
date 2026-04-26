<?php

namespace App\Imports\Asset;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use App\Models\AssetType;
use App\Models\Store;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithValidation;

class AssetsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;
    /**
     * Rows that failed validation — returned to the caller.
     *
     * Each entry:
     * [
     *   'row'    => int,          // 1-based Excel row (header = row 1, first data row = row 2)
     *   'errors' => string[],     // human-readable messages
     * ]
     */
    private array $failures = [];

    /**
     * Number of rows successfully imported.
     */
    private int $importedCount = 0;

    // -------------------------------------------------------------------------
    // Pre-load lookup maps once — avoids N+1 DB queries inside the loop
    // -------------------------------------------------------------------------

    /** @var array<string, int>  lower-cased name => id */
    private array $assetTypeMap = [];

    /** @var array<string, int>  lower-cased name => id */
    private array $storeMap = [];

    /** @var array<string, bool>  existing asset names (lower-cased) */
    private array $existingAssetNames = [];

    /** @var array<string, bool>  names seen in this batch (lower-cased) */
    private array $seenInBatch = [];

    public function __construct()
    {
        $this->assetTypeMap = AssetType::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id])
            ->toArray();

        $this->storeMap = Store::pluck('id', 'title')
            ->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id])
            ->toArray();

        $this->existingAssetNames = Asset::pluck('name')
            ->mapWithKeys(fn ($name) => [strtolower(trim($name)) => true])
            ->toArray();
    }

    // -------------------------------------------------------------------------
    // Main collection handler
    // -------------------------------------------------------------------------

    public function collection(Collection $rows): void
    {
        // Excel row index: heading = 1, first data row = 2
        $excelRow = 1;

        $validRows = [];

        // Pre-compute a base code once before the loop to avoid same-code for all rows
        $baseCode = Asset::generateUniqueAssetCode();
        $codeOffset = 0;

        foreach ($rows as $row) {
            $excelRow++;

            $rowErrors = $this->validateRow($row->toArray(), $excelRow);

            if (!empty($rowErrors)) {
                $this->failures[] = [
                    'row'    => $excelRow,
                    'errors' => $rowErrors,
                ];
                continue;
            }

            // All valid — resolve foreign keys and build insert payload
            $assetTypeName = strtolower(trim($row['asset_category']));
            $storeName     = strtolower(trim($row['store_name'] ?? ''));

            $validRows[] = [
                'asset_type_id'   => $this->assetTypeMap[$assetTypeName],
                'store_id'        => $storeName !== '' ? ($this->storeMap[$storeName] ?? null) : null,
                'name'            => trim($row['asset_name']),
                'asset_code'      => (string)((int)$baseCode + $codeOffset++),
                'has_kv_slot'     => $this->toTinyInt($row['has_kv_slot'] ?? null),
                'minimum_fee'     => $row['minimum_fee'] ?? 0,
                'asset_price'     => $row['asset_price'] ?? 0,
                'is_common_asset' => $this->toTinyInt($row['is_common_asset'] ?? 0),
                'has_self'        => $this->toTinyInt($row['has_self'] ?? null),
                'total_self'      => $this->toTinyInt($row['total_self'] ?? null),
                'status'          => $this->toTinyInt($row['status'] ?? 1),
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            // Mark name as seen so within-batch duplicates are caught
            $this->seenInBatch[strtolower(trim($row['asset_name']))] = true;
        }

        // Bulk insert in a transaction for atomicity
        if (!empty($validRows)) {
            DB::transaction(function () use ($validRows) {
                foreach (array_chunk($validRows, 500) as $chunk) {
                    Asset::insert($chunk);
                }
            });

            $this->importedCount = count($validRows);
        }
    }

    // -------------------------------------------------------------------------
    // Row-level validation
    // -------------------------------------------------------------------------

    private function validateRow(array $row, int $excelRow): array
    {
        $errors = [];

        // --- Required fields ---
        if (empty($row['asset_name'])) {
            $errors[] = 'Asset Name is required.';
        }

//        if (empty($row['asset_code'])) {
//            $errors[] = 'Asset Code is required.';
//        }

        if (empty($row['asset_category'])) {
            $errors[] = 'Asset Category is required.';
        }

        // Stop early if the basics are missing
        if (!empty($errors)) {
            return $errors;
        }

        // --- Asset Category must exist ---
        $assetTypeName = strtolower(trim($row['asset_category']));
        if (!isset($this->assetTypeMap[$assetTypeName])) {
            $errors[] = "Asset Category \"{$row['asset_category']}\" does not exist in the database.";
        }

        // --- Store Name must exist when provided ---
        $storeName = strtolower(trim($row['store_name'] ?? ''));
        if ($storeName !== '' && !isset($this->storeMap[$storeName])) {
            $errors[] = "Store Name \"{$row['store_name']}\" does not exist in the database.";
        }

        // --- Asset Name must be unique (DB + within this batch) ---
        $assetNameKey = strtolower(trim($row['asset_name']));
        if (isset($this->existingAssetNames[$assetNameKey])) {
            $errors[] = "Asset Name \"{$row['asset_name']}\" already exists in the database.";
        } elseif (isset($this->seenInBatch[$assetNameKey])) {
            $errors[] = "Asset Name \"{$row['asset_name']}\" is duplicated within the import file.";
        }

        // --- Asset Code uniqueness ---
//        if (!empty($row['asset_code'])) {
//            $exists = Asset::where('asset_code', trim($row['asset_code']))->exists();
//            if ($exists) {
//                $errors[] = "Asset Code \"{$row['asset_code']}\" already exists in the database.";
//            }
//        }

        // --- Numeric / tinyInt fields ---
        foreach (['has_kv_slot', 'is_common_asset', 'has_self', 'status'] as $field) {
            if (isset($row[$field]) && $row[$field] !== '' && $row[$field] !== null) {
                if (!is_numeric($row[$field]) || !in_array((int) $row[$field], [0, 1], true)) {
                    $errors[] = ucwords(str_replace('_', ' ', $field)) . " must be 0 or 1 (got \"{$row[$field]}\").";
                }
            }
        }

        // --- Decimal fields ---
//        foreach (['minimum_fee', 'asset_price'] as $field) {
//            if (isset($row[$field]) && $row[$field] !== '' && $row[$field] !== null) {
//                if (!is_numeric($row[$field]) || $row[$field] < 0) {
//                    $errors[] = ucwords(str_replace('_', ' ', $field)) . " must be a non-negative number (got \"{$row[$field]}\").";
//                }
//            }
//        }

        return $errors;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function toTinyInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    // -------------------------------------------------------------------------
    // Result accessors
    // -------------------------------------------------------------------------

    public function getFailures(): array
    {
        return $this->failures;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function hasFailures(): bool
    {
        return !empty($this->failures);
    }
}
