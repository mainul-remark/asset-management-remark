<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class ThanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = database_path('seeders/sql/thanas.sql');

        if (!File::exists($sqlPath)) {
            throw new RuntimeException("Thana seed data file not found: {$sqlPath}");
        }

        DB::table('thanas')->truncate();
        DB::unprepared(File::get($sqlPath));
    }
}
