@extends('layouts.app')

@section('title', 'Novo Médico')

@section('content')
<div class="px-4 sm:px-0">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Novo Médico</h1>
        <p class="mt-2 text-sm text-gray-600">Cadastre um novo médico no sistema</p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form method="POST" action="{{ route('medicos.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Nome -->
                <div>
                    <label for="nome" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Nome <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nome"
                        id="nome"
                        value="{{ old('nome') }}"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                        placeholder="Nome completo"
                    />
                    @error('nome')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- CRM -->
                <div>
                    <label for="crm" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        CRM <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="crm"
                        id="crm"
                        value="{{ old('crm') }}"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                        placeholder="CRM"
                    />
                    @error('crm')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Especialidade -->
                <div>
                    <label for="especialidade" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Especialidade <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="especialidade"
                        id="especialidade"
                        value="{{ old('especialidade') }}"
                        required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                        placeholder="Especialidade"
                    />
                    @error('especialidade')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Telefone -->
                <div>
                    <label for="telefone" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Telefone
                    </label>
                    <input
                        type="text"
                        name="telefone"
                        id="telefone"
                        value="{{ old('telefone') }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                        placeholder="Telefone"
                    />
                    @error('telefone')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="sm:col-span-2">
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        E-mail
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500"
                        placeholder="E-mail"
                    />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-4">
                <a href="{{ route('medicos.index') }}" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
                    Cancelar
                </a>
                <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:focus:ring-indigo-800">
                    Cadastrar Médico
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

