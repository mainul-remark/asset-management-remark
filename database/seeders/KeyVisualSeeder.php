<?php

namespace Database\Seeders;

use App\Models\KeyVisual;
use Illuminate\Database\Seeder;

class KeyVisualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KeyVisual::factory()
            ->count(5)
            ->create();
    }
}
