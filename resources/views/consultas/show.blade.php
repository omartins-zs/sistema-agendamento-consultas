@extends('layouts.app')

@section('title', 'Detalhes da Consulta')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('consultas.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 mb-4 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Voltar para consultas
        </a>
        <h1 class="text-4xl font-bold text-gray-900">Detalhes da Consulta</h1>
    </div>

    <!-- Card Principal -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <!-- Header do Card -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-white">Consulta #{{ $consulta->id }}</h2>
                    <div class="mt-2 flex items-center space-x-4 text-indigo-100">
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ \Carbon\Carbon::parse($consulta->data_consulta)->format('d/m/Y') }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ \Carbon\Carbon::parse($consulta->horario_inicio)->format('H:i') }} - 
                            {{ \Carbon\Carbon::parse($consulta->horario_fim)->format('H:i') }}
                        </span>
                    </div>
                </div>
                <div>
                    @php
                        $statusConfig = [
                            'agendada' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-300'],
                            'cancelada' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-300'],
                            'realizada' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-300'],
                            'remarcada' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-300'],
                        ];
                        $status = $statusConfig[$consulta->status] ?? $statusConfig['agendada'];
                    @endphp
                    <span class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-bold {{ $status['bg'] }} {{ $status['text'] }} border-2 {{ $status['border'] }}">
                        {{ ucfirst($consulta->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Conteúdo -->
        <div class="px-8 py-6">
            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Paciente -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Paciente
                    </dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $consulta->paciente->nome }}</dd>
                    <dd class="text-sm text-gray-600 mt-1">CPF: {{ $consulta->paciente->cpf }}</dd>
                </div>

                <!-- Médico -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Médico
                    </dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $consulta->medico->nome }}</dd>
                    <dd class="text-sm text-gray-600 mt-1">{{ $consulta->medico->especialidade }} - CRM: {{ $consulta->medico->crm }}</dd>
                </div>

                <!-- Sala -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Sala
                    </dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $consulta->sala->codigo }}</dd>
                    <dd class="text-sm text-gray-600 mt-1">{{ $consulta->sala->andar }} - {{ $consulta->sala->tipo }}</dd>
                </div>

                <!-- Tipo de Consulta -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Tipo de Consulta
                    </dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $consulta->tipo_consulta->label() }}</dd>
                </div>

                <!-- Tipo de Agendamento -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <dt class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Tipo de Agendamento
                    </dt>
                    <dd class="text-lg font-semibold text-gray-900">{{ $consulta->tipo_agendamento->label() }}</dd>
                </div>

                @if($consulta->motivo_cancelamento)
                    <div class="sm:col-span-2 bg-red-50 rounded-lg p-4 border-2 border-red-200">
                        <dt class="text-sm font-semibold text-red-700 uppercase tracking-wide mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            Motivo do Cancelamento
                        </dt>
                        <dd class="text-sm text-red-900 font-medium">{{ $consulta->motivo_cancelamento }}</dd>
                    </div>
                @endif

                @if($consulta->remarcadaDe)
                    <div class="sm:col-span-2 bg-yellow-50 rounded-lg p-4 border-2 border-yellow-200">
                        <dt class="text-sm font-semibold text-yellow-700 uppercase tracking-wide mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Remarcada de
                        </dt>
                        <dd class="text-sm text-yellow-900">
                            <a href="{{ route('consultas.show', $consulta->remarcadaDe) }}" class="font-semibold hover:text-yellow-700 underline">
                                Consulta #{{ $consulta->remarcadaDe->id }} - 
                                {{ \Carbon\Carbon::parse($consulta->remarcadaDe->data_consulta)->format('d/m/Y') }}
                            </a>
                        </dd>
                    </div>
                @endif

                @if($consulta->remarcacoes->isNotEmpty())
                    <div class="sm:col-span-2 bg-blue-50 rounded-lg p-4 border-2 border-blue-200">
                        <dt class="text-sm font-semibold text-blue-700 uppercase tracking-wide mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Remarcações
                        </dt>
                        <dd class="mt-2">
                            <ul class="space-y-2">
                                @foreach($consulta->remarcacoes as $remarcacao)
                                    <li>
                                        <a href="{{ route('consultas.show', $remarcacao) }}" class="text-sm font-semibold text-blue-900 hover:text-blue-700 underline flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                            Consulta #{{ $remarcacao->id }} - 
                                            {{ \Carbon\Carbon::parse($remarcacao->data_consulta)->format('d/m/Y') }} às 
                                            {{ \Carbon\Carbon::parse($remarcacao->horario_inicio)->format('H:i') }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>

        <!-- Ações -->
        @if($consulta->status == 'agendada')
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-4">
                <a href="{{ route('consultas.edit', $consulta) }}" class="inline-flex items-center px-6 py-3 border-2 border-indigo-300 text-indigo-700 font-semibold rounded-lg hover:bg-indigo-50 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Editar
                </a>
                <button onclick="document.getElementById('cancelar-form').classList.toggle('hidden')" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancelar Consulta
                </button>
            </div>

            <!-- Form de Cancelamento -->
            <div id="cancelar-form" class="hidden px-8 py-6 bg-red-50 border-t-2 border-red-200">
                <form method="POST" action="{{ route('consultas.cancelar', $consulta) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="motivo_cancelamento" class="block text-sm font-semibold text-red-700 mb-2">Motivo do Cancelamento *</label>
                        <textarea name="motivo_cancelamento" id="motivo_cancelamento" rows="3" required class="block w-full rounded-lg border-red-300 shadow-sm focus:border-red-500 focus:ring-2 focus:ring-red-500 focus:outline-none transition-colors">{{ old('motivo_cancelamento') }}</textarea>
                        @error('motivo_cancelamento')
                            <p class="mt-1 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <div class="flex items-center justify-end gap-4">
                        <button type="button" onclick="document.getElementById('cancelar-form').classList.add('hidden')" class="px-6 py-2 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Confirmar Cancelamento
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
