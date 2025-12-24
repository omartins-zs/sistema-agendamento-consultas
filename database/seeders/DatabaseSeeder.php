<?php

namespace Database\Seeders;

use App\Enums\TipoAgendamento;
use App\Enums\TipoConsulta;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Sala;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário padrão
        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@sistema.com',
        ]);

        // Criar médicos (mínimo 5, criando 8 para ter variedade)
        $medicos = Medico::factory(8)->create();

        // Criar pacientes (mínimo 5, criando 15 para ter variedade)
        $pacientes = Paciente::factory(15)->create();

        // Criar salas (mínimo 5, criando 8 com tipos variados)
        $salas = collect([
            Sala::factory()->consultorioIndividual()->create(['codigo' => 'SALA-101', 'andar' => '1º Andar']),
            Sala::factory()->consultorioIndividual()->create(['codigo' => 'SALA-102', 'andar' => '1º Andar']),
            Sala::factory()->salaProcedimentos()->create(['codigo' => 'SALA-201', 'andar' => '2º Andar']),
            Sala::factory()->salaExames()->create(['codigo' => 'SALA-202', 'andar' => '2º Andar']),
            Sala::factory()->salaCirurgia()->create(['codigo' => 'SALA-301', 'andar' => '3º Andar']),
            Sala::factory()->consultorioIndividual()->create(['codigo' => 'SALA-103', 'andar' => '1º Andar']),
            Sala::factory()->salaProcedimentos()->create(['codigo' => 'SALA-203', 'andar' => '2º Andar']),
            Sala::factory()->create(['codigo' => 'SALA-302', 'andar' => '3º Andar', 'tipo' => 'Sala de Ultrassom']),
        ]);

        // Criar consultas agendadas normais (mínimo 5)
        Consulta::factory(8)->agendada()->create([
            'medico_id' => fn () => $medicos->random()->id,
            'paciente_id' => fn () => $pacientes->random()->id,
            'sala_id' => fn () => $salas->where('tipo', 'Consultório Individual')->random()->id ?? $salas->random()->id,
            'tipo_consulta' => TipoConsulta::Normal->value,
            'tipo_agendamento' => TipoAgendamento::Normal->value,
        ]);

        // Criar consultas de exames (mínimo 5)
        Consulta::factory(7)->agendada()->create([
            'medico_id' => fn () => $medicos->random()->id,
            'paciente_id' => fn () => $pacientes->random()->id,
            'sala_id' => fn () => $salas->where('tipo', 'Sala de Exames')->random()->id ?? $salas->random()->id,
            'tipo_consulta' => TipoConsulta::Exame->value,
            'tipo_agendamento' => TipoAgendamento::Normal->value,
        ]);

        // Criar consultas de procedimentos (mínimo 5)
        Consulta::factory(6)->agendada()->create([
            'medico_id' => fn () => $medicos->random()->id,
            'paciente_id' => fn () => $pacientes->random()->id,
            'sala_id' => fn () => $salas->where('tipo', 'Sala de Procedimentos')->random()->id ?? $salas->random()->id,
            'tipo_consulta' => TipoConsulta::Procedimento->value,
            'tipo_agendamento' => TipoAgendamento::Normal->value,
        ]);

        // Criar consultas de cirurgia (mínimo 5)
        Consulta::factory(5)->agendada()->create([
            'medico_id' => fn () => $medicos->random()->id,
            'paciente_id' => fn () => $pacientes->random()->id,
            'sala_id' => fn () => $salas->where('tipo', 'Sala de Cirurgia Ambulatorial')->random()->id ?? $salas->random()->id,
            'tipo_consulta' => TipoConsulta::Cirurgia->value,
            'tipo_agendamento' => TipoAgendamento::Normal->value,
        ]);

        // Criar consultas canceladas (mínimo 5)
        Consulta::factory(6)->cancelada()->create([
            'medico_id' => fn () => $medicos->random()->id,
            'paciente_id' => fn () => $pacientes->random()->id,
            'sala_id' => fn () => $salas->random()->id,
            'data_consulta' => fake()->dateTimeBetween('-15 days', 'now')->format('Y-m-d'),
        ]);

        // Criar consultas realizadas (mínimo 5)
        Consulta::factory(8)->realizada()->create([
            'medico_id' => fn () => $medicos->random()->id,
            'paciente_id' => fn () => $pacientes->random()->id,
            'sala_id' => fn () => $salas->random()->id,
            'data_consulta' => fake()->dateTimeBetween('-30 days', '-1 day')->format('Y-m-d'),
        ]);

        // Criar consultas de encaixe (mínimo 5)
        Consulta::factory(6)->encaixe()->agendada()->create([
            'medico_id' => fn () => $medicos->random()->id,
            'paciente_id' => fn () => $pacientes->random()->id,
            'sala_id' => fn () => $salas->random()->id,
        ]);

        // Criar consultas reagendadas (mínimo 5)
        $consultasParaRemarcar = Consulta::factory(5)->create([
            'medico_id' => fn () => $medicos->random()->id,
            'paciente_id' => fn () => $pacientes->random()->id,
            'sala_id' => fn () => $salas->random()->id,
            'status' => 'remarcada',
            'data_consulta' => fake()->dateTimeBetween('-10 days', '-1 day')->format('Y-m-d'),
        ]);

        foreach ($consultasParaRemarcar as $consultaOriginal) {
            Consulta::factory()->reagendamento()->agendada()->create([
                'medico_id' => $consultaOriginal->medico_id,
                'paciente_id' => $consultaOriginal->paciente_id,
                'sala_id' => $consultaOriginal->sala_id,
                'remarcada_de' => $consultaOriginal->id,
                'tipo_consulta' => $consultaOriginal->tipo_consulta,
            ]);
        }
    }
}
