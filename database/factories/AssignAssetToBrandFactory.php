<?php

namespace Database\Factories;

use App\Models\AssignAssetToBrand;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignAssetToBrandFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssignAssetToBrand::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement([0, 1]);

        return [
            'asset_charge' => $this->faker->randomFloat(2, 0, 5000),
            'close_date' => $this->faker->optional()->date(),
            'status' => $status,
            'is_asset_assigned_currently' => $status,
            'asset_id' => \App\Models\Asset::factory(),
            'brand_id' => \App\Models\Brand::factory(),
            'assigned_by_user_id' => \App\Models\User::factory(),
        ];
    }
}
