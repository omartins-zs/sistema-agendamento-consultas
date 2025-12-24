<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PacienteController extends Controller
{
    public function index(Request $request): View
    {
        $query = Paciente::query();

        if ($request->filled('search')) {
            $query->where('nome', 'like', '%'.$request->search.'%')
                ->orWhere('cpf', 'like', '%'.$request->search.'%');
        }

        $pacientes = $query->orderBy('nome')->paginate(15);

        return view('pacientes.index', compact('pacientes'));
    }

    public function create(): View
    {
        return view('pacientes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'cpf' => ['required', 'string', 'size:11', 'unique:pacientes,cpf'],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        Paciente::create($validated);

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente cadastrado com sucesso!');
    }

    public function show(Paciente $paciente): View
    {
        $paciente->load('consultas.medico', 'consultas.sala');

        return view('pacientes.show', compact('paciente'));
    }

    public function edit(Paciente $paciente): View
    {
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, Paciente $paciente): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'cpf' => ['required', 'string', 'size:11', 'unique:pacientes,cpf,'.$paciente->id],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $paciente->update($validated);

        return redirect()->route('pacientes.show', $paciente)
            ->with('success', 'Paciente atualizado com sucesso!');
    }

    public function destroy(Paciente $paciente): RedirectResponse
    {
        $paciente->delete();

        return redirect()->route('pacientes.index')
            ->with('success', 'Paciente excluído com sucesso!');
    }
}
