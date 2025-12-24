<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PacienteResource;
use App\Models\Paciente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PacienteController extends Controller
{
    #[OA\Get(
        path: '/pacientes',
        tags: ['Pacientes'],
        summary: 'Listar pacientes',
        description: 'Retorna uma lista paginada de pacientes cadastrados. Permite busca por nome ou CPF.',
        parameters: [
            new OA\Parameter(
                name: 'search',
                in: 'query',
                required: false,
                description: 'Busca por nome ou CPF',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de pacientes retornada com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 200),
                        new OA\Property(property: 'message', type: 'string', example: 'Pacientes listados com sucesso.'),
                        new OA\Property(property: 'data', type: 'object'),
                    ]
                )
            ),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Paciente::query();

        if ($request->filled('search')) {
            $query->where('nome', 'like', '%'.$request->search.'%')
                ->orWhere('cpf', 'like', '%'.$request->search.'%');
        }

        $pacientes = $query->orderBy('nome')->paginate(15);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Pacientes listados com sucesso.',
            'data' => PacienteResource::collection($pacientes)->response()->getData(true),
        ], 200);
    }

    #[OA\Post(
        path: '/pacientes',
        tags: ['Pacientes'],
        summary: 'Criar paciente',
        description: 'Cadastra um novo paciente no sistema',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nome', 'cpf', 'data_nascimento'],
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Maria Santos', maxLength: 100),
                    new OA\Property(property: 'cpf', type: 'string', example: '12345678901', minLength: 11, maxLength: 11),
                    new OA\Property(property: 'data_nascimento', type: 'string', format: 'date', example: '1990-05-15'),
                    new OA\Property(property: 'telefone', type: 'string', example: '(11) 91234-5678', nullable: true, maxLength: 20),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'maria.santos@email.com', nullable: true, maxLength: 100),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Paciente criado com sucesso',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'status', type: 'string', example: 'success'),
                        new OA\Property(property: 'status_code', type: 'integer', example: 201),
                        new OA\Property(property: 'message', type: 'string', example: 'Paciente cadastrado com sucesso.'),
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
            'cpf' => ['required', 'string', 'size:11', 'unique:pacientes,cpf'],
            'data_nascimento' => ['required', 'date', 'before:today'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $paciente = Paciente::create($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 201,
            'message' => 'Paciente cadastrado com sucesso.',
            'data' => new PacienteResource($paciente),
        ], 201);
    }

    #[OA\Get(
        path: '/pacientes/{id}',
        tags: ['Pacientes'],
        summary: 'Buscar paciente',
        description: 'Retorna os dados de um paciente específico',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do paciente',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paciente encontrado'),
            new OA\Response(response: 404, description: 'Paciente não encontrado'),
        ]
    )]
    public function show(Paciente $paciente): JsonResponse
    {
        $paciente->load('consultas.medico', 'consultas.sala');

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Paciente encontrado com sucesso.',
            'data' => new PacienteResource($paciente),
        ], 200);
    }

    #[OA\Put(
        path: '/pacientes/{id}',
        tags: ['Pacientes'],
        summary: 'Atualizar paciente',
        description: 'Atualiza os dados de um paciente existente',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do paciente',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nome', type: 'string', example: 'Maria Santos', maxLength: 100),
                    new OA\Property(property: 'cpf', type: 'string', example: '12345678901', minLength: 11, maxLength: 11),
                    new OA\Property(property: 'data_nascimento', type: 'string', format: 'date', example: '1990-05-15'),
                    new OA\Property(property: 'telefone', type: 'string', example: '(11) 91234-5678', nullable: true, maxLength: 20),
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'maria.santos@email.com', nullable: true, maxLength: 100),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Paciente atualizado com sucesso'),
            new OA\Response(response: 404, description: 'Paciente não encontrado'),
            new OA\Response(response: 422, description: 'Erro de validação'),
        ]
    )]
    public function update(Request $request, Paciente $paciente): JsonResponse
    {
        $validated = $request->validate([
            'nome' => ['sometimes', 'required', 'string', 'max:100'],
            'cpf' => ['sometimes', 'required', 'string', 'size:11', 'unique:pacientes,cpf,'.$paciente->id],
            'data_nascimento' => ['sometimes', 'required', 'date', 'before:today'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:100'],
        ]);

        $paciente->update($validated);

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Paciente atualizado com sucesso.',
            'data' => new PacienteResource($paciente->fresh()),
        ], 200);
    }

    #[OA\Delete(
        path: '/pacientes/{id}',
        tags: ['Pacientes'],
        summary: 'Excluir paciente',
        description: 'Exclui um paciente do sistema',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID do paciente',
                schema: new OA\Schema(type: 'integer')
            ),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Paciente excluído com sucesso'),
            new OA\Response(response: 404, description: 'Paciente não encontrado'),
        ]
    )]
    public function destroy(Paciente $paciente): JsonResponse
    {
        $paciente->delete();

        return response()->json([
            'status' => 'success',
            'status_code' => 200,
            'message' => 'Paciente excluído com sucesso.',
            'data' => null,
        ], 200);
    }
}
