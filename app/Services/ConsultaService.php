<?php

namespace App\Services;

use App\Enums\TipoAgendamento;
use App\Models\Consulta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConsultaService
{
    public function verificarConflitoMedico(int $medicoId, string $dataConsulta, string $horarioInicio, string $horarioFim, ?int $consultaId = null): bool
    {
        $consultasExistentes = Consulta::where('medico_id', $medicoId)
            ->where('data_consulta', $dataConsulta)
            ->where('status', 'agendada');

        if ($consultaId) {
            $consultasExistentes->where('id', '!=', $consultaId);
        }

        $consultas = $consultasExistentes->get(['horario_inicio', 'horario_fim']);

        foreach ($consultas as $consulta) {
            // Dois intervalos se sobrepõem se: inicio1 < fim2 AND fim1 > inicio2
            if ($consulta->horario_inicio < $horarioFim && $consulta->horario_fim > $horarioInicio) {
                return true;
            }
        }

        return false;
    }

    public function verificarConflitoSala(int $salaId, string $dataConsulta, string $horarioInicio, string $horarioFim, ?int $consultaId = null): bool
    {
        $consultasExistentes = Consulta::where('sala_id', $salaId)
            ->where('data_consulta', $dataConsulta)
            ->where('status', 'agendada');

        if ($consultaId) {
            $consultasExistentes->where('id', '!=', $consultaId);
        }

        $consultas = $consultasExistentes->get(['horario_inicio', 'horario_fim']);

        foreach ($consultas as $consulta) {
            // Dois intervalos se sobrepõem se: inicio1 < fim2 AND fim1 > inicio2
            if ($consulta->horario_inicio < $horarioFim && $consulta->horario_fim > $horarioInicio) {
                return true;
            }
        }

        return false;
    }

    public function verificarAntecedenciaMinima(string $dataConsulta, string $horarioInicio, int $horasMinimas = 2): bool
    {
        $dataHoraConsulta = Carbon::parse($dataConsulta.' '.$horarioInicio);
        $horasAntecedencia = now()->diffInHours($dataHoraConsulta, false);

        return $horasAntecedencia >= $horasMinimas;
    }

    public function criarConsulta(array $dados): Consulta
    {
        return DB::transaction(function () use ($dados) {
            return Consulta::create($dados);
        });
    }

    public function atualizarConsulta(Consulta $consulta, array $dados): Consulta
    {
        return DB::transaction(function () use ($consulta, $dados) {
            $consulta->update($dados);

            return $consulta->fresh();
        });
    }

    public function cancelarConsulta(Consulta $consulta, string $motivo): Consulta
    {
        return DB::transaction(function () use ($consulta, $motivo) {
            $consulta->update([
                'status' => 'cancelada',
                'motivo_cancelamento' => $motivo,
            ]);

            return $consulta->fresh();
        });
    }

    public function remarcarConsulta(Consulta $consulta, array $dadosNovaConsulta): Consulta
    {
        return DB::transaction(function () use ($consulta, $dadosNovaConsulta) {
            // Cria nova consulta vinculada à original
            $novaConsulta = Consulta::create([
                ...$dadosNovaConsulta,
                'tipo_agendamento' => TipoAgendamento::Reagendamento->value,
                'remarcada_de' => $consulta->id,
            ]);

            // Atualiza consulta original para status remarcada
            $consulta->update([
                'status' => 'remarcada',
            ]);

            return $novaConsulta;
        });
    }
}
