<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\ConsultaController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\SalaController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('agenda.index');
});

Route::resource('medicos', MedicoController::class);
Route::resource('pacientes', PacienteController::class);
Route::resource('salas', SalaController::class);

Route::resource('consultas', ConsultaController::class);
Route::post('consultas/{consulta}/cancelar', [ConsultaController::class, 'cancelar'])->name('consultas.cancelar');
Route::post('consultas/{consulta}/remarcar', [ConsultaController::class, 'remarcar'])->name('consultas.remarcar');

Route::get('agenda', [AgendaController::class, 'index'])->name('agenda.index');
