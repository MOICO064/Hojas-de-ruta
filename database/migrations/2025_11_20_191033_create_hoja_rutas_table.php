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
        Schema::create('hoja_ruta', function (Blueprint $table) {
            $table->id();
            $table->string('idgral', 50);
            $table->string('numero_unidad', 10);

            $table->boolean('externo')->default(false);
            $table->string('nombre_solicitante')->nullable();

            $table->foreignId('unidad_origen_id')->constrained('unidades');
            $table->foreignId('solicitante_id')->nullable()->constrained('funcionarios');

            $table->date('fecha_creacion');
            $table->string('cite')->nullable();
            $table->string('prioridad', 20)->default('normal');
            $table->string('asunto');
            $table->enum('estado', ['pendiente', 'en_proceso', 'concluido', 'anulado']);
            $table->string('gestion', 10);
            $table->integer('fojas')->nullable();
            $table->foreignId('creado_por')->constrained('funcionarios');
            $table->dateTime('fecha_impresion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hoja_ruta');
    }
};
