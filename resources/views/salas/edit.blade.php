@extends('layouts.app')

@section('title', 'Editar Sala')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <a href="{{ route('salas.show', $sala) }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-block">
            ← Voltar para detalhes
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Editar Sala</h1>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('salas.update', $sala) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="codigo" class="block text-sm font-medium text-gray-700">Código *</label>
                    <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $sala->codigo) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('codigo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="andar" class="block text-sm font-medium text-gray-700">Andar</label>
                    <input type="text" name="andar" id="andar" value="{{ old('andar', $sala->andar) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('andar')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo</label>
                    <input type="text" name="tipo" id="tipo" value="{{ old('tipo', $sala->tipo) }}" placeholder="Ex: Consultório Individual, Sala de Procedimentos" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:outline-none">
                    @error('tipo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-4">
                <a href="{{ route('salas.show', $sala) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Atualizar Sala
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

