<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tutor>
 */
class TutorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // 'age'=> $this->faker->numberBetween(18,100),
            'age' => fake()->numberBetween(18, 100),
            'email' => fake()->unique()->safeEmail(),
            'number' => fake()->phoneNumber(),
        ];
    }
}
