<?php

namespace App\Enums;

enum TipoAgendamento: string
{
    case Normal = 'normal';
    case Encaixe = 'encaixe';
    case Reagendamento = 'reagendamento';

    public function label(): string
    {
        return match ($this) {
            self::Normal => 'Agendamento Normal',
            self::Encaixe => 'Encaixe',
            self::Reagendamento => 'Reagendamento',
        };
    }
}
