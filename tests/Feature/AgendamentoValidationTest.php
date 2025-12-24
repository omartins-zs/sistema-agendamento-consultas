<?php

use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Sala;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('valida campos obrigatórios ao criar consulta', function () {
    $response = $this->post(route('consultas.store'), []);

    $response->assertSessionHasErrors(['paciente_id', 'medico_id', 'sala_id', 'data_consulta', 'horario_inicio', 'horario_fim']);
});

it('valida que data não pode ser no passado', function () {
    $paciente = Paciente::factory()->create();
    $medico = Medico::factory()->create();
    $sala = Sala::factory()->create();

    $response = $this->post(route('consultas.store'), [
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala->id,
        'data_consulta' => now()->subDay()->format('Y-m-d'),
        'horario_inicio' => '10:00',
        'horario_fim' => '11:00',
    ]);

    $response->assertSessionHasErrors(['data_consulta']);
});

it('valida que horário fim deve ser posterior ao horário início', function () {
    $paciente = Paciente::factory()->create();
    $medico = Medico::factory()->create();
    $sala = Sala::factory()->create();

    $response = $this->post(route('consultas.store'), [
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala->id,
        'data_consulta' => now()->addDay()->format('Y-m-d'),
        'horario_inicio' => '11:00',
        'horario_fim' => '10:00',
    ]);

    $response->assertSessionHasErrors(['horario_fim']);
});

it('valida antecedência mínima de 2 horas', function () {
    $paciente = Paciente::factory()->create();
    $medico = Medico::factory()->create();
    $sala = Sala::factory()->create();

    $response = $this->post(route('consultas.store'), [
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala->id,
        'data_consulta' => now()->format('Y-m-d'),
        'horario_inicio' => now()->addHour()->format('H:i'),
        'horario_fim' => now()->addHours(2)->format('H:i'),
    ]);

    $response->assertSessionHasErrors(['horario_inicio']);
});

it('permite criar consulta com dados válidos', function () {
    $paciente = Paciente::factory()->create();
    $medico = Medico::factory()->create();
    $sala = Sala::factory()->create();

    $response = $this->post(route('consultas.store'), [
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala->id,
        'data_consulta' => now()->addDays(2)->format('Y-m-d'),
        'horario_inicio' => '10:00',
        'horario_fim' => '11:00',
    ]);

    $response->assertRedirect(route('consultas.index'));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('consultas', [
        'paciente_id' => $paciente->id,
        'medico_id' => $medico->id,
        'sala_id' => $sala->id,
        'status' => 'agendada',
    ]);
});
