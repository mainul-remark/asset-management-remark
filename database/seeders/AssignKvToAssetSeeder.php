<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssignKvToAsset;

class AssignKvToAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssignKvToAsset::factory()
            ->count(5)
            ->create();
    }
}
