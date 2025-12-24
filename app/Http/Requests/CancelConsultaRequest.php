<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelConsultaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motivo_cancelamento' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo_cancelamento.required' => 'O motivo do cancelamento é obrigatório.',
            'motivo_cancelamento.string' => 'O motivo do cancelamento deve ser um texto.',
            'motivo_cancelamento.max' => 'O motivo do cancelamento não pode ter mais de 255 caracteres.',
        ];
    }
}
