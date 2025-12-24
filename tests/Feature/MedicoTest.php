<?php

use App\Models\Medico;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exibe lista de médicos', function () {
    Medico::factory()->count(5)->create();

    $response = $this->get(route('medicos.index'));

    $response->assertSuccessful();
    $response->assertSee('Médicos');
});

it('permite criar novo médico', function () {
    $response = $this->post(route('medicos.store'), [
        'nome' => 'Dr. João Silva',
        'crm' => 'CRM-123456',
        'especialidade' => 'Cardiologia',
        'telefone' => '(11) 99999-9999',
        'email' => 'joao@example.com',
    ]);

    $response->assertRedirect(route('medicos.index'));
    $this->assertDatabaseHas('medicos', [
        'nome' => 'Dr. João Silva',
        'crm' => 'CRM-123456',
    ]);
});

it('valida campos obrigatórios ao criar médico', function () {
    $response = $this->post(route('medicos.store'), []);

    $response->assertSessionHasErrors(['nome', 'crm', 'especialidade']);
});

it('valida CRM único', function () {
    Medico::factory()->create(['crm' => 'CRM-123456']);

    $response = $this->post(route('medicos.store'), [
        'nome' => 'Dr. Teste',
        'crm' => 'CRM-123456',
        'especialidade' => 'Teste',
    ]);

    $response->assertSessionHasErrors(['crm']);
});

it('permite atualizar médico', function () {
    $medico = Medico::factory()->create();

    $response = $this->put(route('medicos.update', $medico), [
        'nome' => 'Dr. João Atualizado',
        'crm' => $medico->crm,
        'especialidade' => 'Ortopedia',
        'telefone' => $medico->telefone,
        'email' => $medico->email,
    ]);

    $response->assertRedirect(route('medicos.show', $medico));
    $this->assertDatabaseHas('medicos', [
        'id' => $medico->id,
        'nome' => 'Dr. João Atualizado',
    ]);
});

it('permite excluir médico', function () {
    $medico = Medico::factory()->create();

    $response = $this->delete(route('medicos.destroy', $medico));

    $response->assertRedirect(route('medicos.index'));
    $this->assertDatabaseMissing('medicos', ['id' => $medico->id]);
});
