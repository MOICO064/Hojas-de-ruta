<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('anulaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoja_id')->nullable()->constrained('hoja_ruta')->nullOnDelete();
            $table->foreignId('derivacion_id')->nullable()->constrained('derivaciones')->nullOnDelete();
            $table->foreignId('funcionario_id')->constrained('funcionarios');
            $table->text('justificacion');
            $table->timestamp('fecha_anulacion')->useCurrent();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anulacions');
    }
};