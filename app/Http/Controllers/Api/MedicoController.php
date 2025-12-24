<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MedicoResource;
use App\Models\Medico;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MedicoController extends Controller
{
    #[OA\Get(
        path: '/medicos',
        tags: ['Médicos'],
        summary: 'Listar médicos',
        description: 'Retorna uma lista paginada de médicos cadastrados',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de médicos retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Médicos listados com sucesso.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $medicos = Medico::orderBy('nome')->paginate(15);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Médicos listados com sucesso.',
            'data' => MedicoResource::collection($medicos)->response()->getData(true),
        ], 200);
    }

    #[OA\Post(
        path: '/medicos',
        tags: ['Médicos'],
        summary: 'Criar médico',
        description: 'Cadastra um novo médico no sistema',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome', 'crm', 'especialidade'],
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Dr. João Silva', maxLength: 100),
                    new OA\Property(property: 'crm', type: 'string', example: '12345-SP', maxLength: 20),
                    new OA\Property(property: 'especialidade', type: 'string', example: 'Cardiologia', maxLength: 100),
                    new OA\Property(property: 'telefone', type: 'string', example: '(11) 98765-4321', nullable: true, maxLength: 20),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao.silva@email.com', nullable: true, maxLength: 100),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Médico criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 201),
                        new OA\Property(property: 'message', type: 'string', example: 'Médico cadastrado com sucesso.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:100'],
            'crm' => ['required', 'string', 'max:20', 'unique:medicos,crm'],
            'especialidade' => ['required', 'string', 'max:100'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $medico = Medico::create($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Médico cadastrado com sucesso.',
            'data' => new MedicoResource($medico),
        ], 201);
    }

    #[OA\Get(
        path: '/medicos/{id}',
        tags: ['Médicos'],
        summary: 'Buscar médico',
        description: 'Retorna os dados de um médico específico',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do médico',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Médico encontrado'),
            new OA\Response(response: 404, description: 'Médico não encontrado'),
        ]
    )]
    public function show(Medico $medico): JsonResponse
    {
        $medico->load('consultas.paciente', 'consultas.sala');

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Médico encontrado com sucesso.',
            'data' => new MedicoResource($medico),
        ], 200);
    }

    #[OA\Put(
        path: '/medicos/{id}',
        tags: ['Médicos'],
        summary: 'Atualizar médico',
        description: 'Atualiza os dados de um médico existente',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do médico',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Dr. João Silva', maxLength: 100),
                    new OA\Property(property: 'crm', type: 'string', example: '12345-SP', maxLength: 20),
                    new OA\Property(property: 'especialidade', type: 'string', example: 'Cardiologia', maxLength: 100),
                    new OA\Property(property: 'telefone', type: 'string', example: '(11) 98765-4321', nullable: true, maxLength: 20),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'joao.silva@email.com', nullable: true, maxLength: 100),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Médico atualizado com sucesso'),
            new OA\Response(response: 404, description: 'Médico não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(Request $request, Medico $medico): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'required', 'string', 'max:100'],
            'crm' => ['sometimes', 'required', 'string', 'max:20', 'unique:medicos,crm,'.$medico->id],
            'especialidade' => ['sometimes', 'required', 'string', 'max:100'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $medico->update($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Médico atualizado com sucesso.',
            'data' => new MedicoResource($medico->fresh()),
        ], 200);
    }

    #[OA\Delete(
        path: '/medicos/{id}',
        tags: ['Médicos'],
        summary: 'Excluir médico',
        description: 'Exclui um médico do sistema',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do médico',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Médico excluído com sucesso'),
            new OA\Response(response: 404, description: 'Médico não encontrado'),
        ]
    )]
    public function destroy(Medico $medico): JsonResponse
    {
        $medico->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Médico excluído com sucesso.',
            'data' => null,
        ], 200);
    }
}
