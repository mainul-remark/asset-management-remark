<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\AssignAssetToStore;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignAssetToStoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssignAssetToStore::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'assign_date' => $this->faker->text(255),
            'asset_charge' => $this->faker->randomNumber(1),
            'asset_id' => \App\Models\Asset::factory(),
            'store_id' => \App\Models\Store::factory(),
            'assigned_by_user_id' => \App\Models\User::factory(),
        ];
    }
}
