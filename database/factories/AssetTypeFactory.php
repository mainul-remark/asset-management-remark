<?php

namespace Database\Factories;

use App\Models\AssetType;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssetType::class;

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
            'height' => $this->faker->randomFloat(2, 0, 9999),
            'width' => $this->faker->randomFloat(2, 0, 9999),
            'depth' => $this->faker->randomNumber(1),
            'dimension_unit_name' => 'px',
            'default_price' => $this->faker->randomNumber(1),
            'status' => $this->faker->numberBetween(0, 127),
            'is_digital' => $this->faker->numberBetween(0, 127),
            'total_self' => $this->faker->numberBetween(0, 127),
            'has_kv_space' => $this->faker->numberBetween(0, 127),
        ];
    }
}
