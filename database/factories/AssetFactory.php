<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Support\Str;
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
        return [
            'name' => $this->faker->name(),
            'default_image' => $this->faker->text(255),
            'asset_code' => $this->faker->unique->text(255),
            'has_kv_slot' => $this->faker->numberBetween(0, 127),
            'minimum_fee' => $this->faker->randomNumber(1),
            'asset_price' => $this->faker->randomNumber(1),
            'is_common_asset' => $this->faker->numberBetween(0, 127),
            'planogram_pdf' => $this->faker->text(255),
            'status' => $this->faker->numberBetween(0, 127),
            'has_self' => $this->faker->numberBetween(0, 127),
            'total_self' => $this->faker->numberBetween(0, 127),
            'asset_type_id' => \App\Models\AssetType::factory(),
            'store_id' => \App\Models\Store::factory(),
        ];
    }
}
