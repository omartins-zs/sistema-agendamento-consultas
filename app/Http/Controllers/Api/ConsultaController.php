<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CancelConsultaRequest;
use App\Http\Requests\StoreConsultaRequest;
use App\Http\Requests\UpdateConsultaRequest;
use App\Http\Resources\ConsultaResource;
use App\Models\Consulta;
use App\Services\ConsultaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ConsultaController extends Controller
{
    public function __construct(
        protected ConsultaService $consultaService
    ) {}

    #[OA\Get(
        path: '/consultas',
        tags: ['Consultas'],
        summary: 'Listar consultas',
        description: 'Retorna uma lista paginada de consultas com filtros opcionais',
        parameters: [
            new OA\Parameter(
                name: 'status',
                in: 'query',
                required: false,
                description: 'Filtrar por status: agendada, cancelada, realizada, remarcada',
                schema: new OA\Schema(type: 'string', enum: ['agendada', 'cancelada', 'realizada', 'remarcada'])
            ),
            new OA\Parameter(
                name: 'medico_id',
                in: 'query',
                required: false,
                description: 'Filtrar por ID do médico',
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'data_consulta',
                in: 'query',
                required: false,
                description: 'Filtrar por data (formato: Y-m-d)',
                schema: new OA\Schema(type: 'string', format: 'date')
            ),
            new OA\Parameter(
                name: 'tipo_consulta',
                in: 'query',
                required: false,
                description: 'Filtrar por tipo: normal, exame, procedimento, cirurgia',
                schema: new OA\Schema(type: 'string', enum: ['normal', 'exame', 'procedimento', 'cirurgia'])
            ),
            new OA\Parameter(
                name: 'tipo_agendamento',
                in: 'query',
                required: false,
                description: 'Filtrar por tipo: normal, encaixe, reagendamento',
                schema: new OA\Schema(type: 'string', enum: ['normal', 'encaixe', 'reagendamento'])
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de consultas retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Consultas listadas com sucesso.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
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

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Consultas listadas com sucesso.',
            'data' => ConsultaResource::collection($consultas)->response()->getData(true),
        ], 200);
    }

    #[OA\Post(
        path: '/consultas',
        tags: ['Consultas'],
        summary: 'Criar consulta',
        description: 'Agenda uma nova consulta. Valida conflitos de horário, antecedência mínima de 2 horas (exceto encaixes).',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['paciente_id', 'medico_id', 'sala_id', 'data_consulta', 'horario_inicio', 'horario_fim'],
                properties: [
                    new OA\Property(property: 'paciente_id', type: 'integer', example: 1),
                    new OA\Property(property: 'medico_id', type: 'integer', example: 1),
                    new OA\Property(property: 'sala_id', type: 'integer', example: 1),
                    new OA\Property(property: 'data_consulta', type: 'string', format: 'date', example: '2025-01-20'),
                    new OA\Property(property: 'horario_inicio', type: 'string', format: 'time', example: '14:00'),
                    new OA\Property(property: 'horario_fim', type: 'string', format: 'time', example: '15:00'),
                    new OA\Property(property: 'tipo_consulta', type: 'string', enum: ['normal', 'exame', 'procedimento', 'cirurgia'], example: 'normal', nullable: true),
                    new OA\Property(property: 'tipo_agendamento', type: 'string', enum: ['normal', 'encaixe', 'reagendamento'], example: 'normal', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Consulta agendada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 201),
                        new OA\Property(property: 'message', type: 'string', example: 'Consulta agendada com sucesso.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(StoreConsultaRequest $request): JsonResponse
    {
        $consulta = $this->consultaService->criarConsulta($request->validated());
        $consulta->load(['paciente', 'medico', 'sala']);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Consulta agendada com sucesso.',
            'data' => new ConsultaResource($consulta),
        ], 201);
    }

    #[OA\Get(
        path: '/consultas/{id}',
        tags: ['Consultas'],
        summary: 'Buscar consulta',
        description: 'Retorna os dados de uma consulta específica',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da consulta',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Consulta encontrada'),
            new OA\Response(response: 404, description: 'Consulta não encontrada'),
        ]
    )]
    public function show(Consulta $consulta): JsonResponse
    {
        $consulta->load(['paciente', 'medico', 'sala', 'remarcadaDe', 'remarcacoes']);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Consulta encontrada com sucesso.',
            'data' => new ConsultaResource($consulta),
        ], 200);
    }

    #[OA\Put(
        path: '/consultas/{id}',
        tags: ['Consultas'],
        summary: 'Atualizar consulta',
        description: 'Atualiza os dados de uma consulta existente',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da consulta',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'paciente_id', type: 'integer', example: 1),
                    new OA\Property(property: 'medico_id', type: 'integer', example: 1),
                    new OA\Property(property: 'sala_id', type: 'integer', example: 1),
                    new OA\Property(property: 'data_consulta', type: 'string', format: 'date', example: '2025-01-21'),
                    new OA\Property(property: 'horario_inicio', type: 'string', format: 'time', example: '15:00'),
                    new OA\Property(property: 'horario_fim', type: 'string', format: 'time', example: '16:00'),
                    new OA\Property(property: 'status', type: 'string', enum: ['agendada', 'cancelada', 'realizada', 'remarcada'], example: 'agendada'),
                    new OA\Property(property: 'tipo_consulta', type: 'string', enum: ['normal', 'exame', 'procedimento', 'cirurgia'], example: 'normal'),
                    new OA\Property(property: 'tipo_agendamento', type: 'string', enum: ['normal', 'encaixe', 'reagendamento'], example: 'normal'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Consulta atualizada com sucesso'),
            new OA\Response(response: 404, description: 'Consulta não encontrada'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(UpdateConsultaRequest $request, Consulta $consulta): JsonResponse
    {
        $consulta = $this->consultaService->atualizarConsulta($consulta, $request->validated());
        $consulta->load(['paciente', 'medico', 'sala']);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Consulta atualizada com sucesso.',
            'data' => new ConsultaResource($consulta),
        ], 200);
    }

    #[OA\Delete(
        path: '/consultas/{id}',
        tags: ['Consultas'],
        summary: 'Excluir consulta',
        description: 'Exclui uma consulta do sistema',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da consulta',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Consulta excluída com sucesso'),
            new OA\Response(response: 404, description: 'Consulta não encontrada'),
        ]
    )]
    public function destroy(Consulta $consulta): JsonResponse
    {
        $consulta->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Consulta excluída com sucesso.',
            'data' => null,
        ], 200);
    }

    #[OA\Post(
        path: '/consultas/{id}/cancelar',
        tags: ['Consultas'],
        summary: 'Cancelar consulta',
        description: 'Cancela uma consulta agendada',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da consulta',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['motivo_cancelamento'],
                properties: [
                    new OA\Property(property: 'motivo_cancelamento', type: 'string', example: 'Paciente não pode comparecer', maxLength: 255),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Consulta cancelada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Consulta cancelada com sucesso.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Consulta não encontrada'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function cancelar(CancelConsultaRequest $request, Consulta $consulta): JsonResponse
    {
        $consulta = $this->consultaService->cancelarConsulta($consulta, $request->motivo_cancelamento);
        $consulta->load(['paciente', 'medico', 'sala']);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Consulta cancelada com sucesso.',
            'data' => new ConsultaResource($consulta),
        ], 200);
    }

    #[OA\Post(
        path: '/consultas/{id}/remarcar',
        tags: ['Consultas'],
        summary: 'Remarcar consulta',
        description: 'Remarca uma consulta (cria nova consulta e marca a original como remarcada)',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da consulta original',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['data_consulta', 'horario_inicio', 'horario_fim'],
                properties: [
                    new OA\Property(property: 'data_consulta', type: 'string', format: 'date', example: '2025-01-25'),
                    new OA\Property(property: 'horario_inicio', type: 'string', format: 'time', example: '10:00'),
                    new OA\Property(property: 'horario_fim', type: 'string', format: 'time', example: '11:00'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Consulta remarcada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 201),
                        new OA\Property(property: 'message', type: 'string', example: 'Consulta remarcada com sucesso.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Consulta não encontrada'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function remarcar(Request $request, Consulta $consulta): JsonResponse
    {
        $validated = $request->validate([
            'data_consulta' => ['required', 'date', 'after_or_equal:today'],
            'horario_inicio' => ['required', 'date_format:H:i'],
            'horario_fim' => ['required', 'date_format:H:i', 'after:horario_inicio'],
        ]);

        $novaConsulta = $this->consultaService->remarcarConsulta($consulta, [
            'paciente_id' => $consulta->paciente_id,
            'medico_id' => $consulta->medico_id,
            'sala_id' => $consulta->sala_id,
            'data_consulta' => $validated['data_consulta'],
            'horario_inicio' => $validated['horario_inicio'],
            'horario_fim' => $validated['horario_fim'],
            'tipo_consulta' => $consulta->tipo_consulta->value,
            'status' => 'agendada',
        ]);

        $novaConsulta->load(['paciente', 'medico', 'sala', 'remarcadaDe']);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Consulta remarcada com sucesso.',
            'data' => new ConsultaResource($novaConsulta),
        ], 201);
    }
}
