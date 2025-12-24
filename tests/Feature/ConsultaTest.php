<?php

use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Sala;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exibe lista de consultas', function () {
    Consulta::factory()->count(5)->create();

    $response = $this->get(route('consultas.index'));

    $response->assertSuccessful();
    $response->assertSee('Consultas');
});

it('exibe formulário de criação de consulta', function () {
    Paciente::factory()->count(3)->create();
    Medico::factory()->count(3)->create();
    Sala::factory()->count(3)->create();

    $response = $this->get(route('consultas.create'));

    $response->assertSuccessful();
    $response->assertSee('Nova Consulta');
});

it('exibe detalhes de uma consulta', function () {
    $consulta = Consulta::factory()->create();

    $response = $this->get(route('consultas.show', $consulta));

    $response->assertSuccessful();
    $response->assertSee($consulta->paciente->nome);
    $response->assertSee($consulta->medico->nome);
});

it('permite editar consulta agendada', function () {
    $consulta = Consulta::factory()->create([
        'status' => 'agendada',
    ]);

    $response = $this->get(route('consultas.edit', $consulta));

    $response->assertSuccessful();
    $response->assertSee('Editar Consulta');
});

it('filtra consultas por status', function () {
    Consulta::factory()->create(['status' => 'agendada']);
    Consulta::factory()->create(['status' => 'cancelada']);

    $response = $this->get(route('consultas.index', ['status' => 'agendada']));

    $response->assertSuccessful();
});

it('filtra consultas por médico', function () {
    $medico = Medico::factory()->create();
    Consulta::factory()->create(['medico_id' => $medico->id]);
    Consulta::factory()->create();

    $response = $this->get(route('consultas.index', ['medico_id' => $medico->id]));

    $response->assertSuccessful();
});
