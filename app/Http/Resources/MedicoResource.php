<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'crm' => $this->crm,
            'especialidade' => $this->especialidade,
            'telefone' => $this->telefone,
            'email' => $this->email,
            'consultas' => $this->whenLoaded('consultas', fn () => ConsultaResource::collection($this->consultas)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
