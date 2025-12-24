<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'paciente' => $this->whenLoaded('paciente', fn () => new PacienteResource($this->paciente)),
            'medico' => $this->whenLoaded('medico', fn () => new MedicoResource($this->medico)),
            'sala' => $this->whenLoaded('sala', fn () => new SalaResource($this->sala)),
            'data_consulta' => $this->data_consulta?->format('Y-m-d'),
            'horario_inicio' => $this->horario_inicio,
            'horario_fim' => $this->horario_fim,
            'status' => $this->status,
            'tipo_consulta' => $this->tipo_consulta?->value,
            'tipo_agendamento' => $this->tipo_agendamento?->value,
            'motivo_cancelamento' => $this->motivo_cancelamento,
            'remarcada_de' => $this->whenLoaded('remarcadaDe', fn () => new ConsultaResource($this->remarcadaDe)),
            'remarcacoes' => $this->whenLoaded('remarcacoes', fn () => ConsultaResource::collection($this->remarcacoes)),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
