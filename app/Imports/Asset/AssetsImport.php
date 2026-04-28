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

class AssetsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    use Importable;

    private array $failures      = [];
    private int   $importedCount = 0;

    // -------------------------------------------------------------------------
    // Pre-loaded lookup maps (avoid N+1 queries inside the loop)
    // -------------------------------------------------------------------------

    /** @var array<string, int>     lc-name => asset_type id */
    private array $assetTypeMap = [];

    /** @var array<string, string>  lc-name => type code (asset_types.code, or first 3 letters of name) */
    private array $assetTypeCodeMap = [];

    /** @var array<string, int>     lc-title => store id */
    private array $storeMap = [];

    /** @var array<string, string>  lc-title => stores.store_code */
    private array $storeCodeMap = [];

    /** @var array<string, bool>    existing asset names, lower-cased */
    private array $existingAssetNames = [];

    /** @var array<string, bool>    names seen in this batch, lower-cased */
    private array $seenInBatch = [];

    /** @var array<string, int>     prefix => next seq number to try */
    private array $prefixNextSeq = [];

    public function __construct()
    {
        AssetType::select('id', 'name', 'code')->get()->each(function ($type) {
            $key = strtolower(trim($type->name));
            $this->assetTypeMap[$key] = $type->id;
            // Use asset_types.code if set, otherwise fall back to first 3 letters of name
            $this->assetTypeCodeMap[$key] = strtoupper(
                !empty($type->code)
                    ? $type->code
                    : substr(preg_replace('/[^a-zA-Z]/', '', $type->name), 0, 3)
            );
        });

        Store::select('id', 'title', 'store_code')->get()->each(function ($store) {
            $key = strtolower(trim($store->title));
            $this->storeMap[$key]     = $store->id;
            $this->storeCodeMap[$key] = strtoupper($store->store_code ?? '');
        });

        $this->existingAssetNames = Asset::withTrashed()->pluck('name')
            ->mapWithKeys(fn ($name) => [strtolower(trim($name)) => true])
            ->toArray();
    }

    // -------------------------------------------------------------------------
    // Main collection handler
    // -------------------------------------------------------------------------

    public function collection(Collection $rows): void
    {
        $excelRow   = 1;
        $validRows  = [];
        $baseCode   = Asset::generateUniqueAssetCode();
        $codeOffset = 0;

        foreach ($rows as $row) {
            $excelRow++;

            $rowErrors = $this->validateRow($row->toArray(), $excelRow);

            if (!empty($rowErrors)) {
                $this->failures[] = ['row' => $excelRow, 'errors' => $rowErrors];
                continue;
            }

            $assetTypeName = strtolower(trim($row['asset_category']));
            $storeName     = strtolower(trim($row['store_name'] ?? ''));

            // Use provided name or auto-generate: {type_code}-{store_code}-{seq}
            $assetName = trim($row['asset_name'] ?? '');
            if ($assetName === '') {
                $assetName = $this->generateName($assetTypeName, $storeName);
            }

            $validRows[] = [
                'asset_type_id'   => $this->assetTypeMap[$assetTypeName],
                'store_id'        => $storeName !== '' ? ($this->storeMap[$storeName] ?? null) : null,
                'name'            => $assetName,
                'asset_code'      => (string) ((int) $baseCode + $codeOffset++),
                'has_kv_slot'     => ($row['has_kv_slot'] ?? '') == 'yes' ? 1 : 0,
                'minimum_fee'     => $row['minimum_fee'] ?? 0,
                'asset_price'     => $row['asset_price'] ?? 0,
                'is_common_asset' => ($row['is_common_asset'] ?? '') == 'yes' ? 1 : 0,
                'has_self'        => ($row['has_self'] ?? '') == 'yes' ? 1 : 0,
                'total_self'      => $this->toTinyInt($row['total_self'] ?? null),
                'status'          => ($row['status'] ?? '') == 'yes' ? 1 : 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];

            $this->seenInBatch[strtolower($assetName)] = true;
        }

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

        // asset_name is optional — auto-generated when blank
        if (empty($row['asset_category'])) {
            $errors[] = 'Asset Category is required.';
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

        // --- If asset_name is provided manually, check uniqueness ---
        $assetName = trim($row['asset_name'] ?? '');
        if ($assetName !== '') {
            $assetNameKey = strtolower($assetName);
            if (isset($this->existingAssetNames[$assetNameKey])) {
                $errors[] = "Asset Name \"{$assetName}\" already exists in the database.";
            } elseif (isset($this->seenInBatch[$assetNameKey])) {
                $errors[] = "Asset Name \"{$assetName}\" is duplicated within the import file.";
            }
        }

        // --- Numeric / tinyInt fields ---
        foreach (['has_kv_slot', 'is_common_asset', 'has_self', 'status'] as $field) {
            if (isset($row[$field]) && $row[$field] !== '' && $row[$field] !== null) {
                if (!is_numeric($row[$field]) || !in_array((int) $row[$field], [0, 1], true)) {
                    $errors[] = ucwords(str_replace('_', ' ', $field)) . " must be 0 or 1 (got \"{$row[$field]}\").";
                }
            }
        }

        return $errors;
    }

    // -------------------------------------------------------------------------
    // Auto name generation: {type_code}-{stores.store_code}-{seq}
    // type_code = asset_types.code if set, else first 3 letters of asset type name
    // -------------------------------------------------------------------------

    private function generateName(string $assetTypeName, string $storeName): string
    {
        $typeCode  = $this->assetTypeCodeMap[$assetTypeName] ?? 'AST';
        $storeCode = $storeName !== '' ? ($this->storeCodeMap[$storeName] ?? 'GEN') : 'GEN';
        $prefix    = $typeCode . '-' . $storeCode . '-';

        if (!isset($this->prefixNextSeq[$prefix])) {
            $this->prefixNextSeq[$prefix] = 1;
        }

        $seq = $this->prefixNextSeq[$prefix];
        while (
            isset($this->existingAssetNames[strtolower($prefix . $seq)]) ||
            isset($this->seenInBatch[strtolower($prefix . $seq)])
        ) {
            $seq++;
        }

        $this->prefixNextSeq[$prefix] = $seq + 1;

        return $prefix . $seq;
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
