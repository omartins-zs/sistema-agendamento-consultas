<?php

namespace App\Enums;

enum TipoConsulta: string
{
    case Normal = 'normal';
    case Exame = 'exame';
    case Procedimento = 'procedimento';
    case Cirurgia = 'cirurgia';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Consulta Normal',
            self::Exame => 'Exame',
            self::Procedimento => 'Procedimento',
            self::Cirurgia => 'Cirurgia',
        };
    }
}
