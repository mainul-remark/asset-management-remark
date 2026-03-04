<?php

namespace Database\Seeders;

use App\Models\KeyVisualSize;
use Illuminate\Database\Seeder;

class KeyVisualSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        KeyVisualSize::factory()
            ->count(5)
            ->create();
    }
}
