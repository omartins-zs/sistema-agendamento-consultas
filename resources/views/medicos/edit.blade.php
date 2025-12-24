@extends('layouts.app')

@section('title', 'Editar Médico')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('medicos.show', $medico) }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">
            ← Voltar para detalhes
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Editar Médico</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('medicos.update', $medico) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome *</label>
                    <input type="text" name="nome" id="nome" value="{{ old('nome', $medico->nome) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('nome')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="crm" class="block text-sm font-medium text-gray-700">CRM *</label>
                    <input type="text" name="crm" id="crm" value="{{ old('crm', $medico->crm) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('crm')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="especialidade" class="block text-sm font-medium text-gray-700">Especialidade *</label>
                    <input type="text" name="especialidade" id="especialidade" value="{{ old('especialidade', $medico->especialidade) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('especialidade')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700">Telefone</label>
                    <input type="text" name="telefone" id="telefone" value="{{ old('telefone', $medico->telefone) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('telefone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">E-mail</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $medico->email) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-4">
                <a href="{{ route('medicos.show', $medico) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Atualizar Médico
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

