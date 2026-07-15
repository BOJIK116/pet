<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Pet>
 */
class PetFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->firstName(),
            'species' => fake()->randomElement([
                'dog',
                'cat',
                'bird',
                'rabbit',
                'rodent',
                'reptile',
                'other',
            ]),
            'breed' => fake()->optional()->word(),
            'sex' => fake()->randomElement([
                'male',
                'female',
                'unknown',
            ]),
            'birth_date' => fake()
                ->optional()
                ->dateTimeBetween('-15 years', 'now')
                ?->format('Y-m-d'),
            'weight' => fake()->optional()->randomFloat(2, 0.1, 80),
            'chronic_conditions' => fake()->optional()->sentence(),
            'is_neutered' => fake()->randomElement([
                true,
                false,
                null,
            ]),
            'notes' => fake()->optional()->sentence(),
            'photo_path' => null,
        ];
    }
}