<?php

use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Sala;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('impede agendamento quando médico já tem consulta no mesmo horário', function () {
    $paciente1 = Paciente::factory()->create();
    $paciente2 = Paciente::factory()->create();
    $medico = Medico::factory()->create();
    $sala1 = Sala::factory()->create();
    $sala2 = Sala::factory()->create();

    $dataConsulta = now()->addDays(3)->format('Y-m-d');

    Consulta::factory()->create([
        'paciente_id' => $paciente1->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala1->id,
        'data_consulta' => $dataConsulta,
        'horario_inicio' => '10:00',
        'horario_fim' => '11:00',
        'status' => 'agendada',
    ]);

    $this->assertDatabaseCount('consultas', 1);

    $response = $this->post(route('consultas.store'), [
        'paciente_id' => $paciente2->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala2->id,
        'data_consulta' => $dataConsulta,
        'horario_inicio' => '10:30',
        'horario_fim' => '11:30',
    ]);

    // Verifica se a consulta não foi criada (deve ter erro de validação)
    $this->assertDatabaseCount('consultas', 1);
    $response->assertSessionHasErrors(['medico_id']);
});

it('impede agendamento quando sala já está ocupada no mesmo horário', function () {
    $paciente1 = Paciente::factory()->create();
    $paciente2 = Paciente::factory()->create();
    $medico1 = Medico::factory()->create();
    $medico2 = Medico::factory()->create();
    $sala = Sala::factory()->create();

    $dataConsulta = now()->addDays(3)->format('Y-m-d');

    Consulta::factory()->create([
        'paciente_id' => $paciente1->id,
        'medico_id' => $medico1->id,
        'sala_id' => $sala->id,
        'data_consulta' => $dataConsulta,
        'horario_inicio' => '10:00',
        'horario_fim' => '11:00',
        'status' => 'agendada',
    ]);

    $this->assertDatabaseCount('consultas', 1);

    $response = $this->post(route('consultas.store'), [
        'paciente_id' => $paciente2->id,
        'medico_id' => $medico2->id,
        'sala_id' => $sala->id,
        'data_consulta' => $dataConsulta,
        'horario_inicio' => '10:30',
        'horario_fim' => '11:30',
    ]);

    // Verifica se a consulta não foi criada (deve ter erro de validação)
    $this->assertDatabaseCount('consultas', 1);
    $response->assertSessionHasErrors(['sala_id']);
});

it('permite agendamento quando horários não se sobrepõem', function () {
    $paciente1 = Paciente::factory()->create();
    $paciente2 = Paciente::factory()->create();
    $medico = Medico::factory()->create();
    $sala = Sala::factory()->create();

    $dataConsulta = now()->addDays(2)->format('Y-m-d');

    Consulta::factory()->create([
        'paciente_id' => $paciente1->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala->id,
        'data_consulta' => $dataConsulta,
        'horario_inicio' => '10:00',
        'horario_fim' => '11:00',
        'status' => 'agendada',
    ]);

    $response = $this->post(route('consultas.store'), [
        'paciente_id' => $paciente2->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala->id,
        'data_consulta' => $dataConsulta,
        'horario_inicio' => '11:00',
        'horario_fim' => '12:00',
    ]);

    $response->assertRedirect(route('consultas.index'));
    $this->assertDatabaseCount('consultas', 2);
});

it('não considera consultas canceladas como conflito', function () {
    $paciente1 = Paciente::factory()->create();
    $paciente2 = Paciente::factory()->create();
    $medico = Medico::factory()->create();
    $sala = Sala::factory()->create();

    $dataConsulta = now()->addDays(2)->format('Y-m-d');

    Consulta::factory()->create([
        'paciente_id' => $paciente1->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala->id,
        'data_consulta' => $dataConsulta,
        'horario_inicio' => '10:00',
        'horario_fim' => '11:00',
        'status' => 'cancelada',
    ]);

    $response = $this->post(route('consultas.store'), [
        'paciente_id' => $paciente2->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala->id,
        'data_consulta' => $dataConsulta,
        'horario_inicio' => '10:00',
        'horario_fim' => '11:00',
    ]);

    $response->assertRedirect(route('consultas.index'));
    $this->assertDatabaseCount('consultas', 2);
});
