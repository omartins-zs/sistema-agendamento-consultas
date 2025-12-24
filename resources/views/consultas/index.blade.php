@extends('layouts.app')

@section('title', 'Consultas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-4xl font-bold text-gray-900">Consultas</h1>
            <p class="mt-2 text-lg text-gray-600">Gerencie todas as consultas agendadas</p>
        </div>
        <a href="{{ route('consultas.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nova Consulta
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
        <form method="GET" action="{{ route('consultas.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" id="status" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors">
                    <option value="">Todos</option>
                    <option value="agendada" {{ request('status') == 'agendada' ? 'selected' : '' }}>Agendada</option>
                    <option value="cancelada" {{ request('status') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                    <option value="realizada" {{ request('status') == 'realizada' ? 'selected' : '' }}>Realizada</option>
                    <option value="remarcada" {{ request('status') == 'remarcada' ? 'selected' : '' }}>Remarcada</option>
                </select>
            </div>
            <div>
                <label for="medico_id" class="block text-sm font-semibold text-gray-700 mb-2">Médico</label>
                <select name="medico_id" id="medico_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors">
                    <option value="">Todos</option>
                    @foreach($medicos as $medico)
                        <option value="{{ $medico->id }}" {{ request('medico_id') == $medico->id ? 'selected' : '' }}>
                            {{ $medico->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="tipo_consulta" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Consulta</label>
                <select name="tipo_consulta" id="tipo_consulta" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors">
                    <option value="">Todos</option>
                    <option value="normal" {{ request('tipo_consulta') == 'normal' ? 'selected' : '' }}>Consulta Normal</option>
                    <option value="exame" {{ request('tipo_consulta') == 'exame' ? 'selected' : '' }}>Exame</option>
                    <option value="procedimento" {{ request('tipo_consulta') == 'procedimento' ? 'selected' : '' }}>Procedimento</option>
                    <option value="cirurgia" {{ request('tipo_consulta') == 'cirurgia' ? 'selected' : '' }}>Cirurgia</option>
                </select>
            </div>
            <div>
                <label for="tipo_agendamento" class="block text-sm font-semibold text-gray-700 mb-2">Tipo de Agendamento</label>
                <select name="tipo_agendamento" id="tipo_agendamento" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors">
                    <option value="">Todos</option>
                    <option value="normal" {{ request('tipo_agendamento') == 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="encaixe" {{ request('tipo_agendamento') == 'encaixe' ? 'selected' : '' }}>Encaixe</option>
                    <option value="reagendamento" {{ request('tipo_agendamento') == 'reagendamento' ? 'selected' : '' }}>Reagendamento</option>
                </select>
            </div>
            <div>
                <label for="data_consulta" class="block text-sm font-semibold text-gray-700 mb-2">Data</label>
                <input type="date" name="data_consulta" id="data_consulta" value="{{ request('data_consulta') }}" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none transition-colors">
            </div>
            <div class="sm:col-span-2 lg:col-span-5 flex items-end gap-2">
                <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    <span class="flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filtrar
                    </span>
                </button>
                <a href="{{ route('consultas.index') }}" class="px-6 py-2.5 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabela -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Data/Hora</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Médico</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sala</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($consultas as $consulta)
                        <tr class="hover:bg-indigo-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ \Carbon\Carbon::parse($consulta->data_consulta)->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-500 flex items-center mt-1">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($consulta->horario_inicio)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($consulta->horario_fim)->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $consulta->paciente->nome }}</div>
                                <div class="text-xs text-gray-500">CPF: {{ $consulta->paciente->cpf }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $consulta->medico->nome }}</div>
                                <div class="text-xs text-gray-500">{{ $consulta->medico->especialidade }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    {{ $consulta->sala->codigo }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $consulta->tipo_consulta->label() }}</div>
                                <div class="text-xs text-gray-500">{{ $consulta->tipo_agendamento->label() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'agendada' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        'cancelada' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        'realizada' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        'remarcada' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                                    ];
                                    $status = $statusConfig[$consulta->status] ?? $statusConfig['agendada'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $status['bg'] }} {{ $status['text'] }}">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $status['icon'] }}"></path>
                                    </svg>
                                    {{ ucfirst($consulta->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('consultas.show', $consulta) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900">Nenhuma consulta encontrada</p>
                                    <p class="text-sm text-gray-500 mt-1">Tente ajustar os filtros ou criar uma nova consulta</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($consultas->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $consultas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
