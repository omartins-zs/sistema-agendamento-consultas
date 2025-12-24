@extends('layouts.app')

@section('title', 'Agenda')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-4xl font-bold text-gray-900">Agenda de Consultas</h1>
        <p class="mt-2 text-lg text-gray-600">Visualize a agenda de consultas por médico e data</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
        <form method="GET" action="{{ route('agenda.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="medico_id" class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Médico
                    </span>
                </label>
                <select name="medico_id" id="medico_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors">
                    <option value="">Selecione um médico</option>
                    @foreach($medicos as $medico)
                        <option value="{{ $medico->id }}" {{ request('medico_id') == $medico->id ? 'selected' : '' }}>
                            {{ $medico->nome }} - {{ $medico->especialidade }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="data_inicio" class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Data Início
                    </span>
                </label>
                <input type="date" name="data_inicio" id="data_inicio" value="{{ $dataInicio }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors">
            </div>
            <div>
                <label for="data_fim" class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Data Fim
                    </span>
                </label>
                <input type="date" name="data_fim" id="data_fim" value="{{ $dataFim }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtrar
                    </span>
                </button>
            </div>
        </form>
    </div>

    @if($medicoSelecionado)
        <!-- Card da Agenda -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <!-- Header do Médico -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-white flex items-center">
                            <svg class="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Dr(a). {{ $medicoSelecionado->nome }}
                        </h2>
                        <p class="text-indigo-100 mt-2 text-lg">{{ $medicoSelecionado->especialidade }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-indigo-100 text-sm font-medium">Período</div>
                        <div class="text-white text-xl font-bold">
                            @if($dataInicio == $dataFim)
                                {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($consultas->isEmpty())
                <!-- Estado Vazio -->
                <div class="px-8 py-16 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-20 h-20 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-xl font-semibold text-gray-900 mb-2">Nenhuma consulta agendada</p>
                        <p class="text-gray-600">Não há consultas agendadas para este médico no período selecionado.</p>
                    </div>
                </div>
            @else
                <!-- Lista de Consultas -->
                <div class="divide-y divide-gray-200">
                    @foreach($consultas as $consulta)
                        <div class="px-8 py-6 hover:bg-indigo-50 transition-colors duration-150">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-6">
                                        <!-- Horário -->
                                        <div class="flex items-center space-x-3">
                                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-500 rounded-lg flex items-center justify-center shadow-md">
                                                <div class="text-center">
                                                    <div class="text-white text-lg font-bold">
                                                        {{ \Carbon\Carbon::parse($consulta->horario_inicio)->format('H:i') }}
                                                    </div>
                                                    <div class="text-indigo-100 text-xs">
                                                        {{ \Carbon\Carbon::parse($consulta->horario_fim)->format('H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Informações do Paciente -->
                                        <div class="flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                <h3 class="text-lg font-bold text-gray-900">{{ $consulta->paciente->nome }}</h3>
                                                @php
                                                    $tipoConfig = [
                                                        'normal' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800'],
                                                        'exame' => ['bg' => 'bg-green-100', 'text' => 'text-green-800'],
                                                        'procedimento' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800'],
                                                        'cirurgia' => ['bg' => 'bg-red-100', 'text' => 'text-red-800'],
                                                    ];
                                                    $tipo = $tipoConfig[$consulta->tipo_consulta->value] ?? $tipoConfig['normal'];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tipo['bg'] }} {{ $tipo['text'] }}">
                                                    {{ $consulta->tipo_consulta->label() }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                                    </svg>
                                                    Sala: {{ $consulta->sala->codigo }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    {{ $consulta->tipo_agendamento->label() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Ação -->
                                <div>
                                    <a href="{{ route('consultas.show', $consulta) }}" class="inline-flex items-center px-4 py-2 bg-indigo-100 text-indigo-700 font-semibold rounded-lg hover:bg-indigo-200 transition-colors">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        Ver detalhes
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <!-- Estado Inicial -->
        <div class="bg-white rounded-xl shadow-lg p-12 border border-gray-100 text-center">
            <div class="flex flex-col items-center">
                <svg class="w-24 h-24 text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-2xl font-bold text-gray-900 mb-2">Selecione um médico e um período</p>
                <p class="text-gray-600">Use os filtros acima para visualizar a agenda de consultas.</p>
            </div>
        </div>
    @endif
</div>
@endsection
