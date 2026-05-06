<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserStoreAssignment;

class UserStoreAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserStoreAssignment::factory()
            ->count(5)
            ->create();
    }
}
