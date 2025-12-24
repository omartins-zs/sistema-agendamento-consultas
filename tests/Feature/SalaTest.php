<?php

use App\Models\Sala;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exibe lista de salas', function () {
    Sala::factory()->count(5)->create();

    $response = $this->get(route('salas.index'));

    $response->assertSuccessful();
    $response->assertSee('Salas');
});

it('permite criar nova sala', function () {
    $response = $this->post(route('salas.store'), [
        'codigo' => 'SALA-101',
        'andar' => '1º Andar',
        'tipo' => 'Consultório Individual',
    ]);

    $response->assertRedirect(route('salas.index'));
    $this->assertDatabaseHas('salas', [
        'codigo' => 'SALA-101',
    ]);
});

it('valida código obrigatório ao criar sala', function () {
    $response = $this->post(route('salas.store'), []);

    $response->assertSessionHasErrors(['codigo']);
});

it('valida código único', function () {
    Sala::factory()->create(['codigo' => 'SALA-101']);

    $response = $this->post(route('salas.store'), [
        'codigo' => 'SALA-101',
    ]);

    $response->assertSessionHasErrors(['codigo']);
});

it('permite atualizar sala', function () {
    $sala = Sala::factory()->create();

    $response = $this->put(route('salas.update', $sala), [
        'codigo' => 'SALA-202',
        'andar' => '2º Andar',
        'tipo' => 'Sala de Procedimentos',
    ]);

    $response->assertRedirect(route('salas.show', $sala));
    $this->assertDatabaseHas('salas', [
        'id' => $sala->id,
        'codigo' => 'SALA-202',
    ]);
});

it('permite excluir sala', function () {
    $sala = Sala::factory()->create();

    $response = $this->delete(route('salas.destroy', $sala));

    $response->assertRedirect(route('salas.index'));
    $this->assertDatabaseMissing('salas', ['id' => $sala->id]);
});
