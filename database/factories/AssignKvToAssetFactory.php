<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\AssignKvToAsset;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignKvToAssetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AssignKvToAsset::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'has_perfect_size_kv' => $this->faker->numberBetween(0, 127),
            'assigned_date' => $this->faker->text(255),
            'instalation_proof' => $this->faker->text(),
            'instalation_status' => 'pending',
            'instalation_date' => $this->faker->text(255),
            'asset_id' => \App\Models\Asset::factory(),
            'key_visual_id' => \App\Models\KeyVisual::factory(),
            'assigned_by' => \App\Models\User::factory(),
            'installed_by' => \App\Models\User::factory(),
            'key_visual_files_id' => \App\Models\KeyVisualFiles::factory(),
        ];
    }
}
