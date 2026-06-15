<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'            => User::factory(),
            'first_name_th'      => fake()->firstName(),
            'last_name_th'       => fake()->lastName(),
            'first_name_en'      => fake()->firstName(),
            'last_name_en'       => fake()->lastName(),
            'date_of_birth'      => fake()->dateTimeBetween('-20 years', '-10 years')->format('Y-m-d'),
            'age'                => fake()->numberBetween(10, 20),
            'grade_level'        => fake()->randomElement(['ม.1', 'ม.2', 'ม.3', 'ม.4', 'ม.5', 'ม.6']),
            'advisor_teacher_id' => null,
            'phone'              => fake()->phoneNumber(),
            'email'              => fake()->optional()->safeEmail(),
            'status'             => 'pending',
        ];
    }

    public function approved(): static
    {
        return $this->state(['status' => 'approved']);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }
}
