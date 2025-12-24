<?php

namespace App\Models;

use App\Enums\TipoAgendamento;
use App\Enums\TipoConsulta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consulta extends Model
{
    use HasFactory;

    protected $fillable = [
        'paciente_id',
        'medico_id',
        'sala_id',
        'data_consulta',
        'horario_inicio',
        'horario_fim',
        'status',
        'tipo_consulta',
        'tipo_agendamento',
        'motivo_cancelamento',
        'remarcada_de',
    ];

    protected function casts(): array
    {
        return [
            'data_consulta' => 'date',
            'tipo_consulta' => TipoConsulta::class,
            'tipo_agendamento' => TipoAgendamento::class,
        ];
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function sala(): BelongsTo
    {
        return $this->belongsTo(Sala::class);
    }

    public function remarcadaDe(): BelongsTo
    {
        return $this->belongsTo(Consulta::class, 'remarcada_de');
    }

    public function remarcacoes(): HasMany
    {
        return $this->hasMany(Consulta::class, 'remarcada_de');
    }

    public function lembretesEnviados(): HasMany
    {
        return $this->hasMany(LembreteEnviado::class);
    }

    public function scopeAgendadas($query)
    {
        return $query->where('status', 'agendada');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('status', 'cancelada');
    }

    public function scopeRealizadas($query)
    {
        return $query->where('status', 'realizada');
    }

    public function scopeRemarcadas($query)
    {
        return $query->where('status', 'remarcada');
    }
}
