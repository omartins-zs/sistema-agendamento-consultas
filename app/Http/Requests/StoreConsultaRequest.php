<?php

namespace App\Http\Requests;

use App\Enums\TipoAgendamento;
use App\Enums\TipoConsulta;
use App\Services\ConsultaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreConsultaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['required', 'exists:medicos,id'],
            'sala_id' => ['required', 'exists:salas,id'],
            'data_consulta' => ['required', 'date', 'after_or_equal:today'],
            'horario_inicio' => ['required', 'date_format:H:i'],
            'horario_fim' => ['required', 'date_format:H:i', 'after:horario_inicio'],
            'tipo_consulta' => ['nullable', Rule::enum(TipoConsulta::class)],
            'tipo_agendamento' => ['nullable', Rule::enum(TipoAgendamento::class)],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $validator->errors()->any()) {
                $service = app(ConsultaService::class);
                $tipoAgendamento = $this->input('tipo_agendamento', TipoAgendamento::Normal->value);

                // Validações que não se aplicam a encaixes
                if ($tipoAgendamento !== TipoAgendamento::Encaixe->value) {
                    $this->validateMedicoDisponibilidade($validator, $service);
                    $this->validateSalaDisponibilidade($validator, $service);
                    $this->validateAntecedenciaMinima($validator, $service);
                }
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tipo_consulta' => $this->input('tipo_consulta', TipoConsulta::Normal->value),
            'tipo_agendamento' => $this->input('tipo_agendamento', TipoAgendamento::Normal->value),
        ]);
    }

    protected function validateMedicoDisponibilidade($validator, ConsultaService $service): void
    {
        $medicoId = $this->input('medico_id');
        $dataConsulta = $this->input('data_consulta');
        $horarioInicio = $this->input('horario_inicio');
        $horarioFim = $this->input('horario_fim');

        if (! $medicoId || ! $dataConsulta || ! $horarioInicio || ! $horarioFim) {
            return;
        }

        if ($service->verificarConflitoMedico((int) $medicoId, $dataConsulta, $horarioInicio, $horarioFim)) {
            $validator->errors()->add('medico_id', 'O médico já possui uma consulta agendada neste horário.');
        }
    }

    protected function validateSalaDisponibilidade($validator, ConsultaService $service): void
    {
        $salaId = $this->input('sala_id');
        $dataConsulta = $this->input('data_consulta');
        $horarioInicio = $this->input('horario_inicio');
        $horarioFim = $this->input('horario_fim');

        if (! $salaId || ! $dataConsulta || ! $horarioInicio || ! $horarioFim) {
            return;
        }

        if ($service->verificarConflitoSala((int) $salaId, $dataConsulta, $horarioInicio, $horarioFim)) {
            $validator->errors()->add('sala_id', 'A sala já está ocupada neste horário.');
        }
    }

    protected function validateAntecedenciaMinima($validator, ConsultaService $service): void
    {
        $dataConsulta = $this->input('data_consulta');
        $horarioInicio = $this->input('horario_inicio');

        if (! $service->verificarAntecedenciaMinima($dataConsulta, $horarioInicio)) {
            $validator->errors()->add('horario_inicio', 'A consulta deve ser agendada com pelo menos 2 horas de antecedência.');
        }
    }

    public function attributes(): array
    {
        return [
            'paciente_id' => 'paciente',
            'medico_id' => 'médico',
            'sala_id' => 'sala',
            'data_consulta' => 'data da consulta',
            'horario_inicio' => 'horário de início',
            'horario_fim' => 'horário de fim',
            'tipo_consulta' => 'tipo de consulta',
            'tipo_agendamento' => 'tipo de agendamento',
        ];
    }

    public function messages(): array
    {
        return [
            'paciente_id.required' => 'O campo :attribute é obrigatório.',
            'paciente_id.exists' => 'O :attribute selecionado não existe.',
            'medico_id.required' => 'O campo :attribute é obrigatório.',
            'medico_id.exists' => 'O :attribute selecionado não existe.',
            'sala_id.required' => 'O campo :attribute é obrigatório.',
            'sala_id.exists' => 'A :attribute selecionada não existe.',
            'data_consulta.required' => 'O campo :attribute é obrigatório.',
            'data_consulta.date' => 'O campo :attribute deve ser uma data válida.',
            'data_consulta.after_or_equal' => 'O campo :attribute não pode ser no passado.',
            'horario_inicio.required' => 'O campo :attribute é obrigatório.',
            'horario_inicio.date_format' => 'O campo :attribute deve estar no formato HH:mm.',
            'horario_fim.required' => 'O campo :attribute é obrigatório.',
            'horario_fim.date_format' => 'O campo :attribute deve estar no formato HH:mm.',
            'horario_fim.after' => 'O campo :attribute deve ser posterior ao horário de início.',
            'tipo_consulta.enum' => 'O campo :attribute deve ser um tipo válido.',
            'tipo_agendamento.enum' => 'O campo :attribute deve ser um tipo válido.',
        ];
    }
}
