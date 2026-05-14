<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\AssignAssetToStore;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Store::query()->exists()) {
            throw new RuntimeException('AssetSeeder requires existing stores so assets can use real store_id values.');
        }

        if (!AssetType::query()->exists()) {
            throw new RuntimeException('AssetSeeder requires existing asset types before seeding assets.');
        }

        $assignedByUserId = User::query()->value('id');

        if (!$assignedByUserId) {
            throw new RuntimeException('AssetSeeder requires at least one user for assign_asset_to_stores logs.');
        }

        Asset::factory()
            ->count(500)
            ->create()
            ->each(fn (Asset $asset) => AssignAssetToStore::assignAssetsToStoreLog($asset, $assignedByUserId));
    }
}
