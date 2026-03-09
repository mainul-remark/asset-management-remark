<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\KeyVisualFiles;
use Illuminate\Database\Eloquent\Factories\Factory;

class KeyVisualFilesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = KeyVisualFiles::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'kv_file' => 'backend/assets/uploaded-files/key-visual-files/sample-' . $this->faker->numberBetween(1000, 9999) . '.jpg',
            'kv_size' => $this->faker->numberBetween(1, 50000),
            'aspect_ratio' => $this->faker->randomFloat(4, 0, 5),
            'file_type' => $this->faker->randomElement(['image/jpeg', 'image/png', 'video/mp4']),
            'file_duration' => $this->faker->text(255),
            'status' => $this->faker->randomElement([0, 1]),
            'key_visual_size_id' => \App\Models\KeyVisualSize::factory(),
            'key_visual_id' => \App\Models\KeyVisual::factory(),
        ];
    }
}
