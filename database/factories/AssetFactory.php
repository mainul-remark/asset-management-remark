<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Asset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        static $assetTypeIds = null;
        static $storeIds = null;

        $assetTypeIds ??= AssetType::query()->pluck('id')->all();
        $storeIds ??= Store::query()->pluck('id')->all();
        $hasSelf = $this->faker->boolean();

        return [
            'asset_type_id' => $assetTypeIds !== []
                ? $this->faker->randomElement($assetTypeIds)
                : AssetType::factory(),
            'name' => ucfirst($this->faker->words(3, true)),
            'default_image' => null,
            'store_id' => $storeIds !== []
                ? $this->faker->randomElement($storeIds)
                : Store::factory(),
            'has_kv_slot' => (int) $this->faker->boolean(),
            'minimum_fee' => $this->faker->randomFloat(2, 0, 5000),
            'asset_price' => $this->faker->randomFloat(2, 100, 50000),
            'is_common_asset' => 0,
            'planogram_pdf' => null,
            'status' => 1,
            'has_self' => (int) $hasSelf,
            'total_self' => $hasSelf ? $this->faker->numberBetween(1, 12) : null,
        ];
    }
}
