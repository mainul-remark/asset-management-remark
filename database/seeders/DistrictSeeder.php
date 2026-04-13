<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PDO;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = $this->loadRows();
        $pdo = DB::connection()->getPdo();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::table('districts')->truncate();

            foreach (array_chunk($rows, 5) as $chunk) {
                $this->insertChunk($chunk, $pdo);
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * Keep the district payload in a companion file because the geometry data
     * makes the raw seed dataset too large to maintain inline in this class.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function loadRows(): array
    {
        $payloadPath = database_path('seeders/data/districts.base64');

        if (! is_file($payloadPath)) {
            throw new \RuntimeException("District snapshot file not found: {$payloadPath}");
        }

        $encoded = preg_replace('/\s+/', '', (string) file_get_contents($payloadPath));

        if ($encoded === null || $encoded === '') {
            throw new \RuntimeException("District snapshot file is empty: {$payloadPath}");
        }

        $compressed = base64_decode($encoded, true);

        if ($compressed === false) {
            throw new \RuntimeException("District snapshot file is not valid base64: {$payloadPath}");
        }

        $json = gzuncompress($compressed);

        if ($json === false) {
            throw new \RuntimeException("District snapshot file could not be decompressed: {$payloadPath}");
        }

        $rows = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($rows)) {
            throw new \RuntimeException("District snapshot did not decode into an array: {$payloadPath}");
        }

        return $rows;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    protected function insertChunk(array $rows, PDO $pdo): void
    {
        $values = [];

        foreach ($rows as $row) {
            $boundaryPolygon = $row['boundary_polygon_wkt'] === null
                ? 'NULL'
                : 'ST_GeomFromText(' . $pdo->quote((string) $row['boundary_polygon_wkt']) . ')';

            $values[] = sprintf(
                '(%d, %d, %s, %s, %s, %s)',
                (int) $row['id'],
                (int) $row['division_id'],
                $pdo->quote((string) $row['name']),
                $boundaryPolygon,
                $this->quoteNullable($pdo, $row['created_at'] ?? null),
                $this->quoteNullable($pdo, $row['updated_at'] ?? null)
            );
        }

        DB::unprepared(
            'INSERT INTO districts (id, division_id, name, boundary_polygon, created_at, updated_at) VALUES '
            . implode(",\n", $values)
        );
    }

    protected function quoteNullable(PDO $pdo, mixed $value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        return $pdo->quote((string) $value);
    }
}
