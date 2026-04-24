<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VisualMerchandising;

class VisualMerchandisingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        VisualMerchandising::factory()
            ->count(5)
            ->create();
    }
}
