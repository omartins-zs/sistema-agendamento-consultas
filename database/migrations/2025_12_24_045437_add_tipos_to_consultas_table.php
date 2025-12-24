<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->enum('tipo_consulta', ['normal', 'exame', 'procedimento', 'cirurgia'])
                ->default('normal')
                ->after('status');
            $table->enum('tipo_agendamento', ['normal', 'encaixe', 'reagendamento'])
                ->default('normal')
                ->after('tipo_consulta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn(['tipo_consulta', 'tipo_agendamento']);
        });
    }
};
