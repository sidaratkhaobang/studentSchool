<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'username'       => fake()->unique()->userName(),
            'email'          => fake()->unique()->safeEmail(),
            'password'       => 'Password1!',
            'role'           => 'student',
            'is_active'      => true,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
