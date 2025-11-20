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
        Schema::create('derivaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoja_id')->constrained('hoja_ruta')->cascadeOnDelete();
            $table->foreignId('unidad_origen_id')->constrained('unidades');
            $table->foreignId('unidad_destino_id')->constrained('unidades');
            $table->text('descripcion')->nullable();
            $table->string('estado')->default('Revision');
            $table->foreignId('derivado_por')->constrained(table: 'funcionarios');

            $table->timestamp('fecha_derivacion')->nullable();
            $table->timestamp('fecha_recepcion')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('derivacions');
    }
};
