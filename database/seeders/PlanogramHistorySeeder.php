<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlanogramHistory;

class PlanogramHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PlanogramHistory::factory()
            ->count(5)
            ->create();
    }
}
