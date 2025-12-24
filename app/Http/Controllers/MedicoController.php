<?php

namespace App\Http\Controllers;

use App\Models\Medico;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicoController extends Controller
{
    public function index(): View
    {
        $medicos = Medico::orderBy('nome')->paginate(15);

        return view('medicos.index', compact('medicos'));
    }

    public function create(): View
    {
        return view('medicos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'crm' => ['required', 'string', 'max:20', 'unique:medicos,crm'],
            'especialidade' => ['required', 'string', 'max:100'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        Medico::create($validated);

        return redirect()->route('medicos.index')
            ->with('success', 'Médico cadastrado com sucesso!');
    }

    public function show(Medico $medico): View
    {
        $medico->load('consultas.paciente', 'consultas.sala');

        return view('medicos.show', compact('medico'));
    }

    public function edit(Medico $medico): View
    {
        return view('medicos.edit', compact('medico'));
    }

    public function update(Request $request, Medico $medico): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'crm' => ['required', 'string', 'max:20', 'unique:medicos,crm,'.$medico->id],
            'especialidade' => ['required', 'string', 'max:100'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $medico->update($validated);

        return redirect()->route('medicos.show', $medico)
            ->with('success', 'Médico atualizado com sucesso!');
    }

    public function destroy(Medico $medico): RedirectResponse
    {
        $medico->delete();

        return redirect()->route('medicos.index')
            ->with('success', 'Médico excluído com sucesso!');
    }
}
