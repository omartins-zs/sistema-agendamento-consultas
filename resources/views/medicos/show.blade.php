@extends('layouts.app')

@section('title', 'Detalhes do Médico')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('medicos.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">
            ← Voltar para médicos
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Detalhes do Médico</h1>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">{{ $medico->nome }}</h2>
                    <p class="text-sm text-gray-600 mt-1">{{ $medico->especialidade }}</p>
                </div>
                <a href="{{ route('medicos.edit', $medico) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Editar
                </a>
            </div>
        </div>

        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">CRM</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medico->crm }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Especialidade</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medico->especialidade }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">Telefone</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medico->telefone ?? 'Não informado' }}</dd>
                </div>

                <div>
                    <dt class="text-sm font-medium text-gray-500">E-mail</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $medico->email ?? 'Não informado' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    @if($medico->consultas->isNotEmpty())
        <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Consultas</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($medico->consultas->take(10) as $consulta)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($consulta->data_consulta)->format('d/m/Y') }} - 
                                    {{ \Carbon\Carbon::parse($consulta->horario_inicio)->format('H:i') }}
                                </div>
                                <div class="text-sm text-gray-500">{{ $consulta->paciente->nome }}</div>
                            </div>
                            <a href="{{ route('consultas.show', $consulta) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                Ver detalhes
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

