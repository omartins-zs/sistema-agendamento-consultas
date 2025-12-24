<?php

use App\Http\Controllers\Api\AgendaController;
use App\Http\Controllers\Api\ConsultaController;
use App\Http\Controllers\Api\MedicoController;
use App\Http\Controllers\Api\PacienteController;
use App\Http\Controllers\Api\SalaController;
use Illuminate\Support\Facades\Route;

Route::apiResource('medicos', MedicoController::class)->names([
    'index' => 'api.medicos.index',
    'store' => 'api.medicos.store',
    'show' => 'api.medicos.show',
    'update' => 'api.medicos.update',
    'destroy' => 'api.medicos.destroy',
]);

Route::apiResource('pacientes', PacienteController::class)->names([
    'index' => 'api.pacientes.index',
    'store' => 'api.pacientes.store',
    'show' => 'api.pacientes.show',
    'update' => 'api.pacientes.update',
    'destroy' => 'api.pacientes.destroy',
]);

Route::apiResource('salas', SalaController::class)->names([
    'index' => 'api.salas.index',
    'store' => 'api.salas.store',
    'show' => 'api.salas.show',
    'update' => 'api.salas.update',
    'destroy' => 'api.salas.destroy',
]);

Route::apiResource('consultas', ConsultaController::class)->names([
    'index' => 'api.consultas.index',
    'store' => 'api.consultas.store',
    'show' => 'api.consultas.show',
    'update' => 'api.consultas.update',
    'destroy' => 'api.consultas.destroy',
]);

Route::post('consultas/{consulta}/cancelar', [ConsultaController::class, 'cancelar'])->name('api.consultas.cancelar');
Route::post('consultas/{consulta}/remarcar', [ConsultaController::class, 'remarcar'])->name('api.consultas.remarcar');

Route::get('agenda', [AgendaController::class, 'index'])->name('api.agenda.index');
