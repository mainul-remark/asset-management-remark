<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\VisualMerchandising;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisualMerchandisingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VisualMerchandising::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'issue_text' => $this->faker->text(),
            'issue_fix_status' => 'pending',
            'status' => $this->faker->numberBetween(0, 127),
            'creator_id' => \App\Models\User::factory(),
            'store_id' => \App\Models\Store::factory(),
            'asset_id' => \App\Models\Asset::factory(),
        ];
    }
}
