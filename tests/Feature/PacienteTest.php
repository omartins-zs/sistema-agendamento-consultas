<?php

use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exibe lista de pacientes', function () {
    Paciente::factory()->count(5)->create();

    $response = $this->get(route('pacientes.index'));

    $response->assertSuccessful();
    $response->assertSee('Pacientes');
});

it('permite criar novo paciente', function () {
    $response = $this->post(route('pacientes.store'), [
        'nome' => 'Maria Silva',
        'cpf' => '12345678901',
        'data_nascimento' => '1990-01-01',
        'telefone' => '(11) 99999-9999',
        'email' => 'maria@example.com',
    ]);

    $response->assertRedirect(route('pacientes.index'));
    $this->assertDatabaseHas('pacientes', [
        'nome' => 'Maria Silva',
        'cpf' => '12345678901',
    ]);
});

it('valida campos obrigatórios ao criar paciente', function () {
    $response = $this->post(route('pacientes.store'), []);

    $response->assertSessionHasErrors(['nome', 'cpf', 'data_nascimento']);
});

it('valida CPF único', function () {
    Paciente::factory()->create(['cpf' => '12345678901']);

    $response = $this->post(route('pacientes.store'), [
        'nome' => 'Teste',
        'cpf' => '12345678901',
        'data_nascimento' => '1990-01-01',
    ]);

    $response->assertSessionHasErrors(['cpf']);
});

it('valida que data de nascimento não pode ser hoje ou futuro', function () {
    $response = $this->post(route('pacientes.store'), [
        'nome' => 'Teste',
        'cpf' => '12345678901',
        'data_nascimento' => now()->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors(['data_nascimento']);
});

it('permite atualizar paciente', function () {
    $paciente = Paciente::factory()->create();

    $response = $this->put(route('pacientes.update', $paciente), [
        'nome' => 'Maria Atualizada',
        'cpf' => $paciente->cpf,
        'data_nascimento' => $paciente->data_nascimento->format('Y-m-d'),
        'telefone' => $paciente->telefone,
        'email' => $paciente->email,
    ]);

    $response->assertRedirect(route('pacientes.show', $paciente));
    $this->assertDatabaseHas('pacientes', [
        'id' => $paciente->id,
        'nome' => 'Maria Atualizada',
    ]);
});

it('permite buscar paciente por nome ou CPF', function () {
    Paciente::factory()->create(['nome' => 'João Silva', 'cpf' => '11111111111']);
    Paciente::factory()->create(['nome' => 'Maria Santos', 'cpf' => '22222222222']);

    $response = $this->get(route('pacientes.index', ['search' => 'João']));

    $response->assertSuccessful();
    $response->assertSee('João Silva');
    $response->assertDontSee('Maria Santos');
});
