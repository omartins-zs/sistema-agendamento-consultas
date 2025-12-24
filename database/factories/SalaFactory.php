<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sala>
 */
class SalaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tipos = [
            'Consultório Individual',
            'Sala de Procedimentos',
            'Consultório Compartilhado',
            'Sala de Exames',
            'Sala de Cirurgia Ambulatorial',
            'Sala de Ultrassom',
            'Sala de Raio-X',
            'Sala de Endoscopia',
            'Consultório de Emergência',
            'Sala de Observação',
        ];

        $andares = [
            'Térreo',
            '1º Andar',
            '2º Andar',
            '3º Andar',
            '4º Andar',
            'Subsolo',
        ];

        return [
            'codigo' => fake()->unique()->bothify('SALA-###'),
            'andar' => fake()->randomElement($andares),
            'tipo' => fake()->randomElement($tipos),
        ];
    }

    /**
     * Indicate that the sala is a consultório individual.
     */
    public function consultorioIndividual(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'Consultório Individual',
        ]);
    }

    /**
     * Indicate that the sala is a sala de procedimentos.
     */
    public function salaProcedimentos(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'Sala de Procedimentos',
        ]);
    }

    /**
     * Indicate that the sala is a sala de exames.
     */
    public function salaExames(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'Sala de Exames',
        ]);
    }

    /**
     * Indicate that the sala is a sala de cirurgia.
     */
    public function salaCirurgia(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo' => 'Sala de Cirurgia Ambulatorial',
        ]);
    }
}
