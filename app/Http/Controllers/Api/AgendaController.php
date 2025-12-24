<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ConsultaResource;
use App\Models\Consulta;
use App\Models\Medico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AgendaController extends Controller
{
    #[OA\Get(
        path: '/agenda',
        tags: ['Agenda'],
        summary: 'Consultar agenda',
        description: 'Consulta a agenda de um médico em um período de datas. Retorna lista de médicos e consultas agendadas.',
        parameters: [
            new OA\Parameter(
                name: 'medico_id',
                in: 'query',
                required: false,
                description: 'ID do médico (obrigatório para listar consultas)',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'data_inicio',
                in: 'query',
                required: false,
                description: 'Data de início do período (formato: Y-m-d). Padrão: data atual',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'data_fim',
                in: 'query',
                required: false,
                description: 'Data de fim do período (formato: Y-m-d). Padrão: data atual',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Agenda consultada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Agenda consultada com sucesso.'),
                        new OA\Property(
                            property: 'data',
                            type: 'object',
                            properties: [
                                new OA\Property(property: 'medicos', type: 'array', items: new OA\Items(type: 'object')),
                                new OA\Property(property: 'medico_selecionado', type: 'object', nullable: true),
                                new OA\Property(property: 'data_inicio', type: 'string', format: 'date'),
                                new OA\Property(property: 'data_fim', type: 'string', format: 'date'),
                                new OA\Property(property: 'consultas', type: 'array', items: new OA\Items(type: 'object')),
                            ]
                        ),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Médico não encontrado'),
        ]
    )]
    public function index(Request $request): JsonResponse
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

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Agenda consultada com sucesso.',
            'data' => [
                'medicos' => $medicos->map(fn ($medico) => [
                    'id' => $medico->id,
                    'nome' => $medico->nome,
                    'crm' => $medico->crm,
                    'especialidade' => $medico->especialidade,
                ]),
                'medico_selecionado' => $medicoSelecionado ? [
                    'id' => $medicoSelecionado->id,
                    'nome' => $medicoSelecionado->nome,
                    'crm' => $medicoSelecionado->crm,
                    'especialidade' => $medicoSelecionado->especialidade,
                ] : null,
                'data_inicio' => $dataInicio,
                'data_fim' => $dataFim,
                'consultas' => ConsultaResource::collection($consultas),
            ],
        ], 200);
    }
}
