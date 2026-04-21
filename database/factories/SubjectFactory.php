<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'subject_code'     => strtoupper(fake()->unique()->lexify('????###')),
            'name_th'          => fake()->words(3, true),
            'name_en'          => fake()->words(3, true),
            'description'      => fake()->optional()->paragraph(),
            'credit_hours'     => fake()->numberBetween(1, 4),
            'hours_per_session' => fake()->numberBetween(1, 3),
            'is_active'        => true,
        ];
    }
}
