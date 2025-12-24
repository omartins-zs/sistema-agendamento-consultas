<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SalaResource;
use App\Models\Sala;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class SalaController extends Controller
{
    #[OA\Get(
        path: '/salas',
        tags: ['Salas'],
        summary: 'Listar salas',
        description: 'Retorna uma lista paginada de salas cadastradas',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de salas retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Salas listadas com sucesso.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $salas = Sala::orderBy('codigo')->paginate(15);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Salas listadas com sucesso.',
            'data' => SalaResource::collection($salas)->response()->getData(true),
        ], 200);
    }

    #[OA\Post(
        path: '/salas',
        tags: ['Salas'],
        summary: 'Criar sala',
        description: 'Cadastra uma nova sala no sistema',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['codigo'],
                properties: [
                    new OA\Property(property: 'codigo', type: 'string', example: 'SALA-101', maxLength: 50),
                    new OA\Property(property: 'andar', type: 'string', example: '1º Andar', nullable: true, maxLength: 20),
                    new OA\Property(property: 'tipo', type: 'string', example: 'Consulta', nullable: true, maxLength: 50),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Sala criada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 201),
                        new OA\Property(property: 'message', type: 'string', example: 'Sala cadastrada com sucesso.'),
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
            'codigo' => ['required', 'string', 'max:50', 'unique:salas,codigo'],
            'andar' => ['nullable', 'string', 'max:20'],
            'tipo' => ['nullable', 'string', 'max:50'],
        ]);

        $sala = Sala::create($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Sala cadastrada com sucesso.',
            'data' => new SalaResource($sala),
        ], 201);
    }

    #[OA\Get(
        path: '/salas/{id}',
        tags: ['Salas'],
        summary: 'Buscar sala',
        description: 'Retorna os dados de uma sala específica',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da sala',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sala encontrada'),
            new OA\Response(response: 404, description: 'Sala não encontrada'),
        ]
    )]
    public function show(Sala $sala): JsonResponse
    {
        $sala->load('consultas.paciente', 'consultas.medico');

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Sala encontrada com sucesso.',
            'data' => new SalaResource($sala),
        ], 200);
    }

    #[OA\Put(
        path: '/salas/{id}',
        tags: ['Salas'],
        summary: 'Atualizar sala',
        description: 'Atualiza os dados de uma sala existente',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da sala',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'codigo', type: 'string', example: 'SALA-101', maxLength: 50),
                    new OA\Property(property: 'andar', type: 'string', example: '1º Andar', nullable: true, maxLength: 20),
                    new OA\Property(property: 'tipo', type: 'string', example: 'Consulta Geral', nullable: true, maxLength: 50),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Sala atualizada com sucesso'),
            new OA\Response(response: 404, description: 'Sala não encontrada'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(Request $request, Sala $sala): JsonResponse
    {
        $validated = $request->validate([
            'codigo' => ['sometimes', 'required', 'string', 'max:50', 'unique:salas,codigo,'.$sala->id],
            'andar' => ['nullable', 'string', 'max:20'],
            'tipo' => ['nullable', 'string', 'max:50'],
        ]);

        $sala->update($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Sala atualizada com sucesso.',
            'data' => new SalaResource($sala->fresh()),
        ], 200);
    }

    #[OA\Delete(
        path: '/salas/{id}',
        tags: ['Salas'],
        summary: 'Excluir sala',
        description: 'Exclui uma sala do sistema',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID da sala',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Sala excluída com sucesso'),
            new OA\Response(response: 404, description: 'Sala não encontrada'),
        ]
    )]
    public function destroy(Sala $sala): JsonResponse
    {
        $sala->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Sala excluída com sucesso.',
            'data' => null,
        ], 200);
    }
}
