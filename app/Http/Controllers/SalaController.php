<?php

namespace App\Http\Controllers;

use App\Models\Sala;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaController extends Controller
{
    public function index(): View
    {
        $salas = Sala::orderBy('codigo')->paginate(15);

        return view('salas.index', compact('salas'));
    }

    public function create(): View
    {
        return view('salas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:salas,codigo'],
            'andar' => ['nullable', 'string', 'max:20'],
            'tipo' => ['nullable', 'string', 'max:50'],
        ]);

        Sala::create($validated);

        return redirect()->route('salas.index')
            ->with('success', 'Sala cadastrada com sucesso!');
    }

    public function show(Sala $sala): View
    {
        $sala->load('consultas.paciente', 'consultas.medico');

        return view('salas.show', compact('sala'));
    }

    public function edit(Sala $sala): View
    {
        return view('salas.edit', compact('sala'));
    }

    public function update(Request $request, Sala $sala): RedirectResponse
    {
        $validated = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:salas,codigo,'.$sala->id],
            'andar' => ['nullable', 'string', 'max:20'],
            'tipo' => ['nullable', 'string', 'max:50'],
        ]);

        $sala->update($validated);

        return redirect()->route('salas.show', $sala)
            ->with('success', 'Sala atualizada com sucesso!');
    }

    public function destroy(Sala $sala): RedirectResponse
    {
        $sala->delete();

        return redirect()->route('salas.index')
            ->with('success', 'Sala excluída com sucesso!');
    }
}
