<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Medico;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(Request $request): View
    {
        $medicos = Medico::orderBy('nome')->get();
        $medicoSelecionado = null;
        $dataInicio = $request->get('data_inicio', now()->format('Y-m-d'));
        $dataFim = $request->get('data_fim', now()->format('Y-m-d'));
        $consultas = collect();

        if ($request->filled('medico_id')) {
            $medicoSelecionado = Medico::findOrFail($request->medico_id);
            $query = Consulta::with(['paciente', 'sala'])
                ->where('medico_id', $request->medico_id)
                ->where('status', 'agendada');

            if ($dataInicio && $dataFim) {
                $query->whereBetween('data_consulta', [$dataInicio, $dataFim]);
            } elseif ($dataInicio) {
                $query->where('data_consulta', '>=', $dataInicio);
            } elseif ($dataFim) {
                $query->where('data_consulta', '<=', $dataFim);
            }

            $consultas = $query->orderBy('data_consulta')->orderBy('horario_inicio')->get();
        }

        return view('agenda.index', compact('medicos', 'medicoSelecionado', 'dataInicio', 'dataFim', 'consultas'));
    }
}
