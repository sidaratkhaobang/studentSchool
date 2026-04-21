<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name_th' => fake()->firstName(),
            'last_name_th'  => fake()->lastName(),
            'first_name_en' => fake()->firstName(),
            'last_name_en'  => fake()->lastName(),
            'email'         => fake()->unique()->safeEmail(),
            'phone'         => fake()->phoneNumber(),
            'bio'           => fake()->optional()->paragraph(),
            'is_active'     => true,
        ];
    }
}
