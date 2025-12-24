<?php

namespace App\Console\Commands;

use App\Jobs\EnviarLembretesConsultas;
use Illuminate\Console\Command;

class EnviarLembretesCommand extends Command
{
    protected $signature = 'consultas:enviar-lembretes';

    protected $description = 'Envia lembretes para pacientes com consultas agendadas para o dia seguinte';

    public function handle(): int
    {
        $this->info('Enviando lembretes de consultas...');

        EnviarLembretesConsultas::dispatch();

        $this->info('Lembretes enviados com sucesso!');

        return Command::SUCCESS;
    }
}
