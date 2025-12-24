<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medico>
 */
class MedicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $especialidades = [
            'Cardiologia',
            'Ortopedia',
            'Pediatria',
            'Dermatologia',
            'Ginecologia',
            'Neurologia',
            'Oftalmologia',
            'Psiquiatria',
            'Urologia',
            'Endocrinologia',
            'Gastroenterologia',
            'Pneumologia',
            'Oncologia',
            'Reumatologia',
            'Otorrinolaringologia',
        ];

        return [
            'nome' => 'Dr(a). '.fake()->name(),
            'crm' => fake()->unique()->numerify('CRM-#######'),
            'especialidade' => fake()->randomElement($especialidades),
            'telefone' => fake()->numerify('(##) #####-####'),
            'email' => fake()->unique()->safeEmail(),
        ];
    }
}
