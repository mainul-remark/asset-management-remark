<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use App\Models\UserStoreAssignment;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserStoreAssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserStoreAssignment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_id' => $this->faker->randomNumber(),
            'status' => $this->faker->numberBetween(0, 127),
            'assigned_at' => $this->faker->dateTime(),
            'store_id' => \App\Models\Store::factory(),
            'user_id' => \App\Models\User::factory(),
            'assigned_by' => \App\Models\User::factory(),
        ];
    }
}
