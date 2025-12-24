<?php

namespace App\Http\Requests;

use App\Enums\TipoAgendamento;
use App\Enums\TipoConsulta;
use App\Services\ConsultaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConsultaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $consulta = $this->route('consulta');

        return [
            'paciente_id' => ['sometimes', 'required', 'exists:pacientes,id'],
            'medico_id' => ['sometimes', 'required', 'exists:medicos,id'],
            'sala_id' => ['sometimes', 'required', 'exists:salas,id'],
            'data_consulta' => ['sometimes', 'required', 'date', 'after_or_equal:today'],
            'horario_inicio' => ['sometimes', 'required', 'date_format:H:i'],
            'horario_fim' => ['sometimes', 'required', 'date_format:H:i', 'after:horario_inicio'],
            'status' => ['sometimes', 'required', Rule::in(['agendada', 'cancelada', 'realizada', 'remarcada'])],
            'tipo_consulta' => ['sometimes', 'required', Rule::enum(TipoConsulta::class)],
            'tipo_agendamento' => ['sometimes', 'required', Rule::enum(TipoAgendamento::class)],
            'motivo_cancelamento' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (! $validator->errors()->any()) {
                $service = app(ConsultaService::class);
                $consulta = $this->route('consulta');
                $tipoAgendamento = $this->input('tipo_agendamento', $consulta->tipo_agendamento->value);

                // Validações que não se aplicam a encaixes
                if ($tipoAgendamento !== TipoAgendamento::Encaixe->value) {
                    $this->validateMedicoDisponibilidade($validator, $service, $consulta->id);
                    $this->validateSalaDisponibilidade($validator, $service, $consulta->id);
                    $this->validateAntecedenciaMinima($validator, $service);
                }
            }
        });
    }

    protected function validateMedicoDisponibilidade($validator, ConsultaService $service, int $consultaId): void
    {
        $medicoId = $this->input('medico_id', $this->route('consulta')->medico_id);
        $dataConsulta = $this->input('data_consulta', $this->route('consulta')->data_consulta->format('Y-m-d'));
        $horarioInicio = $this->input('horario_inicio', $this->route('consulta')->horario_inicio);
        $horarioFim = $this->input('horario_fim', $this->route('consulta')->horario_fim);

        if ($service->verificarConflitoMedico($medicoId, $dataConsulta, $horarioInicio, $horarioFim, $consultaId)) {
            $validator->errors()->add('medico_id', 'O médico já possui uma consulta agendada neste horário.');
        }
    }

    protected function validateSalaDisponibilidade($validator, ConsultaService $service, int $consultaId): void
    {
        $salaId = $this->input('sala_id', $this->route('consulta')->sala_id);
        $dataConsulta = $this->input('data_consulta', $this->route('consulta')->data_consulta->format('Y-m-d'));
        $horarioInicio = $this->input('horario_inicio', $this->route('consulta')->horario_inicio);
        $horarioFim = $this->input('horario_fim', $this->route('consulta')->horario_fim);

        if ($service->verificarConflitoSala($salaId, $dataConsulta, $horarioInicio, $horarioFim, $consultaId)) {
            $validator->errors()->add('sala_id', 'A sala já está ocupada neste horário.');
        }
    }

    protected function validateAntecedenciaMinima($validator, ConsultaService $service): void
    {
        $consulta = $this->route('consulta');
        $dataConsulta = $this->input('data_consulta', $consulta->data_consulta->format('Y-m-d'));
        $horarioInicio = $this->input('horario_inicio', $consulta->horario_inicio);

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
            'status' => 'status',
            'tipo_consulta' => 'tipo de consulta',
            'tipo_agendamento' => 'tipo de agendamento',
            'motivo_cancelamento' => 'motivo do cancelamento',
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
            'status.required' => 'O campo :attribute é obrigatório.',
            'status.in' => 'O campo :attribute deve ser um status válido.',
            'tipo_consulta.required' => 'O campo :attribute é obrigatório.',
            'tipo_consulta.enum' => 'O campo :attribute deve ser um tipo válido.',
            'tipo_agendamento.required' => 'O campo :attribute é obrigatório.',
            'tipo_agendamento.enum' => 'O campo :attribute deve ser um tipo válido.',
            'motivo_cancelamento.string' => 'O campo :attribute deve ser um texto.',
            'motivo_cancelamento.max' => 'O campo :attribute não pode exceder 255 caracteres.',
        ];
    }
}
