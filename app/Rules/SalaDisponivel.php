<?php

namespace App\Rules;

use App\Models\Consulta;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class SalaDisponivel implements DataAwareRule, ValidationRule
{
    protected array $data = [];

    public function __construct(
        public ?int $consultaId = null
    ) {}

    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $dataConsulta = $this->data['data_consulta'] ?? null;
        $horarioInicio = $this->data['horario_inicio'] ?? null;
        $horarioFim = $this->data['horario_fim'] ?? null;

        if (! $dataConsulta || ! $horarioInicio || ! $horarioFim) {
            return;
        }

        $query = Consulta::where('sala_id', $value)
            ->where('data_consulta', $dataConsulta)
            ->where('status', 'agendada')
            ->where(function ($q) use ($horarioInicio, $horarioFim) {
                $q->where(function ($subQuery) use ($horarioInicio) {
                    // Verifica se o início da nova consulta está dentro de uma consulta existente
                    $subQuery->where('horario_inicio', '<=', $horarioInicio)
                        ->where('horario_fim', '>', $horarioInicio);
                })
                    ->orWhere(function ($subQuery) use ($horarioFim) {
                        // Verifica se o fim da nova consulta está dentro de uma consulta existente
                        $subQuery->where('horario_inicio', '<', $horarioFim)
                            ->where('horario_fim', '>=', $horarioFim);
                    })
                    ->orWhere(function ($subQuery) use ($horarioInicio, $horarioFim) {
                        // Verifica se a nova consulta envolve completamente uma consulta existente
                        $subQuery->where('horario_inicio', '>=', $horarioInicio)
                            ->where('horario_fim', '<=', $horarioFim);
                    });
            });

        if ($this->consultaId) {
            $query->where('id', '!=', $this->consultaId);
        }

        if ($query->exists()) {
            $fail('A sala já está ocupada neste horário.');
        }
    }
}
