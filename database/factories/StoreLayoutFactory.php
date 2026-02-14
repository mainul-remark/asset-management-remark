<?php

namespace Database\Factories;

use App\Models\StoreLayout;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreLayoutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StoreLayout::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layout_img' => $this->faker->text(),
            'layout_pdf' => $this->faker->text(),
            'change_log' => $this->faker->text(),
            'changed_at' => $this->faker->text(255),
            'is_currently_active' => $this->faker->numberBetween(0, 127),
            'store_id' => \App\Models\Store::factory(),
        ];
    }
}
