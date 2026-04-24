<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\KeyVisualSize;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeyVisualSizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = KeyVisualSize::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'height' => $this->faker->numberBetween(1, 9999),
            'width' => $this->faker->numberBetween(1, 9999),
            'unit_name' => 'px',
            'status' => $this->faker->randomElement([0, 1]),
        ];
    }
}
