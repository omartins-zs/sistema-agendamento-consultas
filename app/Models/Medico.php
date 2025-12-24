<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'crm',
        'especialidade',
        'telefone',
        'email',
    ];

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class);
    }
}
