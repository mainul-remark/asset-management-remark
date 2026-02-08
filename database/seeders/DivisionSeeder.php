<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        Division::factory()
//            ->count(5)
//            ->create();

        DB::table('divisions')->truncate();

        DB::table('divisions')->insert([
            [
                'id' => 1,
                'name' => 'barisal',
                'created_at' => '2026-01-28 05:11:51',
                'updated_at' => '2026-01-28 05:11:51',
            ],
            [
                'id' => 2,
                'name' => 'chittagong',
                'created_at' => '2026-01-28 05:11:51',
                'updated_at' => '2026-01-28 05:11:51',
            ],
            [
                'id' => 3,
                'name' => 'dhaka',
                'created_at' => '2026-01-28 05:11:51',
                'updated_at' => '2026-01-28 05:11:51',
            ],
            [
                'id' => 4,
                'name' => 'khulna',
                'created_at' => '2026-01-28 05:11:51',
                'updated_at' => '2026-01-28 05:11:51',
            ],
            [
                'id' => 5,
                'name' => 'mymensingh',
                'created_at' => '2026-01-28 05:11:51',
                'updated_at' => '2026-01-28 05:11:51',
            ],
            [
                'id' => 6,
                'name' => 'rajshahi',
                'created_at' => '2026-01-28 05:11:51',
                'updated_at' => '2026-01-28 05:11:51',
            ],
            [
                'id' => 7,
                'name' => 'rangpur',
                'created_at' => '2026-01-28 05:11:51',
                'updated_at' => '2026-01-28 05:11:51',
            ],
            [
                'id' => 8,
                'name' => 'sylhet',
                'created_at' => '2026-01-28 05:11:51',
                'updated_at' => '2026-01-28 05:11:51',
            ],
        ]);

    }
}
