<?php

namespace Database\Factories;

use App\Enums\TipoAgendamento;
use App\Enums\TipoConsulta;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Sala;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consulta>
 */
class ConsultaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Garantir horário comercial válido (08:00 até 17:00)
        $horaInicio = fake()->numberBetween(8, 17);
        $minutoInicio = fake()->randomElement([0, 15, 30, 45]);

        // Duração da consulta: 30min, 45min ou 1h
        $duracaoMinutos = fake()->randomElement([30, 45, 60]);

        $horarioInicio = sprintf('%02d:%02d', $horaInicio, $minutoInicio);

        // Calcular horário fim garantindo que não ultrapasse 18:00
        $timestampInicio = strtotime($horarioInicio);
        $timestampFim = $timestampInicio + ($duracaoMinutos * 60);

        // Se passar das 18:00, ajustar para 18:00
        $timestampLimite = strtotime('18:00');
        if ($timestampFim > $timestampLimite) {
            $timestampFim = $timestampLimite;
        }

        $horarioFim = date('H:i', $timestampFim);

        return [
            'paciente_id' => Paciente::factory(),
            'medico_id' => Medico::factory(),
            'sala_id' => Sala::factory(),
            'data_consulta' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'horario_inicio' => $horarioInicio,
            'horario_fim' => $horarioFim,
            'status' => fake()->randomElement(['agendada', 'cancelada', 'realizada', 'remarcada']),
            'tipo_consulta' => fake()->randomElement(TipoConsulta::cases())->value,
            'tipo_agendamento' => fake()->randomElement(TipoAgendamento::cases())->value,
            'motivo_cancelamento' => fake()->boolean(20) ? fake()->sentence() : null,
            'remarcada_de' => null,
        ];
    }

    public function agendada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'agendada',
        ]);
    }

    public function cancelada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelada',
            'motivo_cancelamento' => fake()->sentence(),
        ]);
    }

    public function realizada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'realizada',
        ]);
    }

    public function encaixe(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_agendamento' => TipoAgendamento::Encaixe->value,
        ]);
    }

    public function reagendamento(): static
    {
        return $this->state(fn (array $attributes) => [
            'tipo_agendamento' => TipoAgendamento::Reagendamento->value,
        ]);
    }
}
