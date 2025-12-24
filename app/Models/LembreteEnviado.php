<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LembreteEnviado extends Model
{
    protected $fillable = [
        'consulta_id',
        'data_envio',
        'canal',
    ];

    protected function casts(): array
    {
        return [
            'data_envio' => 'datetime',
        ];
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }
}
