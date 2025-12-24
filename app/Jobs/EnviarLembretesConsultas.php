<?php

namespace App\Jobs;

use App\Models\Consulta;
use App\Models\LembreteEnviado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EnviarLembretesConsultas implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $amanha = now()->addDay()->format('Y-m-d');

        $consultas = Consulta::with(['paciente', 'medico', 'sala'])
            ->where('data_consulta', $amanha)
            ->where('status', 'agendada')
            ->get();

        foreach ($consultas as $consulta) {
            // Verificar se já foi enviado lembrete por email
            $lembreteEmail = LembreteEnviado::where('consulta_id', $consulta->id)
                ->where('canal', 'email')
                ->whereDate('data_envio', now())
                ->exists();

            if (! $lembreteEmail && $consulta->paciente->email) {
                $this->enviarLembreteEmail($consulta);
            }

            // Verificar se já foi enviado lembrete por SMS
            $lembreteSms = LembreteEnviado::where('consulta_id', $consulta->id)
                ->where('canal', 'sms')
                ->whereDate('data_envio', now())
                ->exists();

            if (! $lembreteSms && $consulta->paciente->telefone) {
                $this->enviarLembreteSms($consulta);
            }
        }

        Log::info("Lembretes enviados para consultas do dia {$amanha}");
    }

    protected function enviarLembreteEmail(Consulta $consulta): void
    {
        // Aqui você pode integrar com um serviço de email real (Mailgun, SendGrid, etc.)
        // Por enquanto, apenas registramos o envio
        LembreteEnviado::create([
            'consulta_id' => $consulta->id,
            'data_envio' => now(),
            'canal' => 'email',
        ]);

        Log::info("Lembrete por email enviado para consulta #{$consulta->id} - Paciente: {$consulta->paciente->nome}");
    }

    protected function enviarLembreteSms(Consulta $consulta): void
    {
        // Aqui você pode integrar com um serviço de SMS real (Twilio, etc.)
        // Por enquanto, apenas registramos o envio
        LembreteEnviado::create([
            'consulta_id' => $consulta->id,
            'data_envio' => now(),
            'canal' => 'sms',
        ]);

        Log::info("Lembrete por SMS enviado para consulta #{$consulta->id} - Paciente: {$consulta->paciente->nome}");
    }
}
