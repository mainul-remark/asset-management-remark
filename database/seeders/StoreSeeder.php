<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sqlPath = database_path('seeders/sql/stores.sql');

        if (! File::exists($sqlPath)) {
            throw new RuntimeException("Store seed data file not found: {$sqlPath}");
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            DB::table('stores')->truncate();
            DB::unprepared(File::get($sqlPath));
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}
