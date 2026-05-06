<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\PlanogramHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanogramHistoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlanogramHistory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'file_path' => $this->faker->text(255),
            'status' => $this->faker->numberBetween(0, 127),
            'changed_date' => $this->faker->dateTime(),
            'store_id' => \App\Models\Store::factory(),
            'asset_id' => \App\Models\Asset::factory(),
            'assigned_by' => \App\Models\User::factory(),
            'brand_id' => \App\Models\Brand::factory(),
        ];
    }
}
