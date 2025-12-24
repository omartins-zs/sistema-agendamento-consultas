<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->onDelete('restrict');
            $table->foreignId('medico_id')->constrained('medicos')->onDelete('restrict');
            $table->foreignId('sala_id')->constrained('salas')->onDelete('restrict');
            $table->date('data_consulta');
            $table->time('horario_inicio');
            $table->time('horario_fim');
            $table->enum('status', ['agendada', 'cancelada', 'realizada', 'remarcada'])->default('agendada');
            $table->string('motivo_cancelamento', 255)->nullable();
            $table->foreignId('remarcada_de')->nullable()->constrained('consultas')->onDelete('set null');
            $table->timestamps();

            $table->index(['medico_id', 'data_consulta'], 'idx_consulta_medico_data');
            $table->index(['sala_id', 'data_consulta'], 'idx_consulta_sala_data');
        });

        // SQLite não suporta ADD CONSTRAINT CHECK, então só aplicamos no MySQL/PostgreSQL
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE consultas ADD CONSTRAINT chk_horarios CHECK (horario_inicio < horario_fim)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
