<?php

namespace App\Http\Controllers;

use App\Http\Requests\CancelConsultaRequest;
use App\Http\Requests\StoreConsultaRequest;
use App\Http\Requests\UpdateConsultaRequest;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Sala;
use App\Services\ConsultaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultaController extends Controller
{
    public function __construct(
        protected ConsultaService $consultaService
    ) {}

    public function index(Request $request): View
    {
        $query = Consulta::with(['paciente', 'medico', 'sala']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('medico_id')) {
            $query->where('medico_id', $request->medico_id);
        }

        if ($request->filled('data_consulta')) {
            $query->where('data_consulta', $request->data_consulta);
        }

        if ($request->filled('tipo_consulta')) {
            $query->where('tipo_consulta', $request->tipo_consulta);
        }

        if ($request->filled('tipo_agendamento')) {
            $query->where('tipo_agendamento', $request->tipo_agendamento);
        }

        $consultas = $query->latest('data_consulta')->latest('horario_inicio')->paginate(15);
        $medicos = Medico::orderBy('nome')->get();

        return view('consultas.index', compact('consultas', 'medicos'));
    }

    public function create(): View
    {
        $pacientes = Paciente::orderBy('nome')->get();
        $medicos = Medico::orderBy('nome')->get();
        $salas = Sala::orderBy('codigo')->get();

        return view('consultas.create', compact('pacientes', 'medicos', 'salas'));
    }

    public function store(StoreConsultaRequest $request): RedirectResponse
    {
        $this->consultaService->criarConsulta($request->validated());

        return redirect()->route('consultas.index')
            ->with('success', 'Consulta agendada com sucesso!');
    }

    public function show(Consulta $consulta): View
    {
        $consulta->load(['paciente', 'medico', 'sala', 'remarcadaDe', 'remarcacoes']);

        return view('consultas.show', compact('consulta'));
    }

    public function edit(Consulta $consulta): View
    {
        $pacientes = Paciente::orderBy('nome')->get();
        $medicos = Medico::orderBy('nome')->get();
        $salas = Sala::orderBy('codigo')->get();

        return view('consultas.edit', compact('consulta', 'pacientes', 'medicos', 'salas'));
    }

    public function update(UpdateConsultaRequest $request, Consulta $consulta): RedirectResponse
    {
        $this->consultaService->atualizarConsulta($consulta, $request->validated());

        return redirect()->route('consultas.show', $consulta)
            ->with('success', 'Consulta atualizada com sucesso!');
    }

    public function destroy(Consulta $consulta): RedirectResponse
    {
        $consulta->delete();

        return redirect()->route('consultas.index')
            ->with('success', 'Consulta excluída com sucesso!');
    }

    public function cancelar(CancelConsultaRequest $request, Consulta $consulta): RedirectResponse
    {
        $this->consultaService->cancelarConsulta($consulta, $request->motivo_cancelamento);

        return redirect()->route('consultas.show', $consulta)
            ->with('success', 'Consulta cancelada com sucesso!');
    }

    public function remarcar(Request $request, Consulta $consulta): RedirectResponse
    {
        $request->validate([
            'data_consulta' => ['required', 'date', 'after_or_equal:today'],
            'horario_inicio' => ['required', 'date_format:H:i'],
            'horario_fim' => ['required', 'date_format:H:i', 'after:horario_inicio'],
        ]);

        $novaConsulta = $this->consultaService->remarcarConsulta($consulta, [
            'paciente_id' => $consulta->paciente_id,
            'medico_id' => $consulta->medico_id,
            'sala_id' => $consulta->sala_id,
            'data_consulta' => $request->data_consulta,
            'horario_inicio' => $request->horario_inicio,
            'horario_fim' => $request->horario_fim,
            'tipo_consulta' => $consulta->tipo_consulta->value,
            'status' => 'agendada',
        ]);

        return redirect()->route('consultas.show', $novaConsulta)
            ->with('success', 'Consulta remarcada com sucesso!');
    }
}
