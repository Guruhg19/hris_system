<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement(['MALE','FEMALE']),
            'age' => $this->faker->numberBetween(18, 65),
            'phone' => $this->faker->phoneNumber(),
            'photo' => $this->faker->imageUrl(640, 480, 'people'),
            'team_id' => $this->faker->numberBetween(1, 30),
            'role_id' => $this->faker->numberBetween(1, 50),
        ];
    }
}
