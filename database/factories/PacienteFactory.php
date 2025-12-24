<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paciente>
 */
class PacienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cpf' => fake()->unique()->numerify('###########'),
            'data_nascimento' => fake()->date('Y-m-d', '-80 years'),
            'telefone' => fake()->numerify('(##) #####-####'),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
