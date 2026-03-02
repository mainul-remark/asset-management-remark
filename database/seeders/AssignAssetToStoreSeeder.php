<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssignAssetToStore;

class AssignAssetToStoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AssignAssetToStore::factory()
            ->count(5)
            ->create();
    }
}
