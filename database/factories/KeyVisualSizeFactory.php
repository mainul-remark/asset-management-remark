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
            'height' => $this->faker->randomFloat(2, 0, 9999),
            'width' => $this->faker->randomFloat(2, 0, 9999),
            'unit_name' => 'px',
            'kv_file' => $this->faker->text(),
            'kv_size' => $this->faker->numberBetween(0, 8388607),
            'aspect_ratio' => $this->faker->randomNumber(2),
            'status' => $this->faker->numberBetween(0, 127),
        ];
    }
}
