<?php

use App\Models\Consulta;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('permite cancelar consulta agendada', function () {
    $consulta = Consulta::factory()->create([
        'status' => 'agendada',
    ]);

    $response = $this->post(route('consultas.cancelar', $consulta), [
        'motivo_cancelamento' => 'Paciente não pode comparecer',
    ]);

    $response->assertRedirect(route('consultas.show', $consulta));
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('consultas', [
        'id' => $consulta->id,
        'status' => 'cancelada',
        'motivo_cancelamento' => 'Paciente não pode comparecer',
    ]);
});

it('requer motivo ao cancelar consulta', function () {
    $consulta = Consulta::factory()->create([
        'status' => 'agendada',
    ]);

    $response = $this->post(route('consultas.cancelar', $consulta), []);

    $response->assertSessionHasErrors(['motivo_cancelamento']);
});

it('permite remarcar consulta agendada', function () {
    $consulta = Consulta::factory()->create([
        'status' => 'agendada',
    ]);

    $novaData = now()->addDays(5)->format('Y-m-d');

    $response = $this->post(route('consultas.remarcar', $consulta), [
        'data_consulta' => $novaData,
        'horario_inicio' => '14:00',
        'horario_fim' => '15:00',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('consultas', [
        'id' => $consulta->id,
        'status' => 'remarcada',
    ]);

    $this->assertDatabaseHas('consultas', [
        'paciente_id' => $consulta->paciente_id,
        'medico_id' => $consulta->medico_id,
        'sala_id' => $consulta->sala_id,
        'horario_inicio' => '14:00',
        'horario_fim' => '15:00',
        'status' => 'agendada',
        'remarcada_de' => $consulta->id,
    ]);

    $novaConsulta = Consulta::where('remarcada_de', $consulta->id)->first();
    expect($novaConsulta)->not->toBeNull();
    expect($novaConsulta->data_consulta->format('Y-m-d'))->toBe($novaData);
});

it('valida dados ao remarcar consulta', function () {
    $consulta = Consulta::factory()->create([
        'status' => 'agendada',
    ]);

    $response = $this->post(route('consultas.remarcar', $consulta), [
        'data_consulta' => now()->subDay()->format('Y-m-d'),
        'horario_inicio' => '15:00',
        'horario_fim' => '14:00',
    ]);

    $response->assertSessionHasErrors(['data_consulta', 'horario_fim']);
});
