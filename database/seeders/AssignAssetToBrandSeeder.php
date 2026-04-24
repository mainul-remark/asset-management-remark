<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssignAssetToBrand;

class AssignAssetToBrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssignAssetToBrand::factory()
            ->count(5)
            ->create();
    }
}
