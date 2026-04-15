<?php

namespace Database\Seeders;

use App\Models\Thana;
use Illuminate\Database\Seeder;

class ThanaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Thana::factory()
            ->count(5)
            ->create();
    }
}
