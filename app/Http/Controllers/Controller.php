<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Sistema de Agendamento de Consultas API',
    description: 'API REST para gerenciamento de agendamento de consultas médicas'
)]
#[OA\Server(
    url: 'http://localhost:8000/api',
    description: 'Servidor de desenvolvimento'
)]
#[OA\Tag(
    name: 'Médicos',
    description: 'Endpoints para gerenciamento de médicos'
)]
#[OA\Tag(
    name: 'Pacientes',
    description: 'Endpoints para gerenciamento de pacientes'
)]
#[OA\Tag(
    name: 'Salas',
    description: 'Endpoints para gerenciamento de salas'
)]
#[OA\Tag(
    name: 'Consultas',
    description: 'Endpoints para gerenciamento de consultas'
)]
#[OA\Tag(
    name: 'Agenda',
    description: 'Endpoints para consulta de agenda'
)]
#[OA\Schema(
    schema: 'ApiResponse',
    properties: [
        new OA\Property(property: 'status', type: 'string', example: 'success'),
        new OA\Property(property: 'status_code', type: 'integer', example: 200),
        new OA\Property(property: 'message', type: 'string', example: 'Operação realizada com sucesso.'),
        new OA\Property(property: 'data', type: 'object'),
    ]
)]
#[OA\Schema(
    schema: 'ApiErrorResponse',
    properties: [
        new OA\Property(property: 'status', type: 'string', example: 'error'),
        new OA\Property(property: 'status_code', type: 'integer', example: 422),
        new OA\Property(property: 'message', type: 'string', example: 'Dados inválidos.'),
        new OA\Property(property: 'errors', type: 'object'),
    ]
)]
abstract class Controller
{
    //
}
