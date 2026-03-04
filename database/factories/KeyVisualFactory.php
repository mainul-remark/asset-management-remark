<?php

namespace Database\Factories;

use App\Models\KeyVisual;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeyVisualFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = KeyVisual::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'unique_code' => $this->faker->unique->text(255),
            'minimum_res_height' => $this->faker->randomNumber(0),
            'minimum_res_width' => $this->faker->randomNumber(0),
            'kv_type' => 'image',
            'kv_sample_file' => $this->faker->text(),
            'kv_thumb' => $this->faker->text(255),
            'status' => $this->faker->numberBetween(0, 127),
            'asset_type_id' => \App\Models\AssetType::factory(),
        ];
    }
}
