<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
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
            'mothers_name' => $this->faker->name(gender: 'female'),
            'birthdate' => $this->faker->date(),
            'cpf' => $this->faker->cpf(formatted: false),
            'cns' => $this->faker->cns(),
        ];
    }
}
