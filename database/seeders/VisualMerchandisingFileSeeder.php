<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisualMerchandisingFile;

class VisualMerchandisingFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VisualMerchandisingFile::factory()
            ->count(5)
            ->create();
    }
}
