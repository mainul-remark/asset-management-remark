<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = database_path('seeders/sql/districts.sql');

        if (!File::exists($sqlPath)) {
            throw new RuntimeException("District seed data file not found: {$sqlPath}");
        }

        DB::table('districts')->truncate();
        DB::unprepared(File::get($sqlPath));
    }
}
