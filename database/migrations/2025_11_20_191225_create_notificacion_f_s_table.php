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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hoja_id')->constrained('hoja_ruta')->cascadeOnDelete();
            $table->foreignId('funcionario_id')->constrained('funcionarios');
            $table->string('tipo', 50);
            $table->text('mensaje');
            $table->timestamp('fecha')->useCurrent();
            $table->boolean('leido')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificacion_f_s');
    }
};
