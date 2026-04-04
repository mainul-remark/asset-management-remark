<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\VisualMerchandisingFile;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisualMerchandisingFileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VisualMerchandisingFile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'file_path' => $this->faker->text(),
            'file_type' => $this->faker->text(255),
            'visual_merchandising_id' => \App\Models\VisualMerchandising::factory(),
        ];
    }
}
