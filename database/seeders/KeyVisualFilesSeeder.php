<?php

namespace Database\Seeders;

use App\Models\KeyVisualFiles;
use Illuminate\Database\Seeder;

class KeyVisualFilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KeyVisualFiles::factory()
            ->count(5)
            ->create();
    }
}
