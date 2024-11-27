<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $range = ['sargento','teniente','capitan','mayor'];
        $gender = rand(0,1);
        return [
            'ci' => fake()->numberBetween(1,1000000),
            'surname' => fake()->lastName($gender),
            'name' => fake()->name($gender),
            'cellular' => fake()->numberBetween(7000000,9000000),
            'range' => $range[rand(0,3)]
        ];
    }
}
