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
            $table->integer('idgral')->default(0);
            $table->integer('numero_unidad');

            $table->boolean('externo')->default(false);
            $table->string('nombre_solicitante')->nullable();

            $table->foreignId('unidad_origen_id')->constrained('unidades');
            $table->foreignId('solicitante_id')->nullable()->constrained('funcionarios');

            $table->date('fecha_creacion');
            $table->string('cite')->nullable();
            $table->boolean('urgente')->default(false);
            $table->string('asunto');
            $table->enum('estado', ['Pendiente', 'En Proceso', 'Concluido', 'Anulado']);
            $table->integer('gestion');
            $table->foreignId('creado_por')->constrained('users');
            $table->dateTime('fecha_impresion')->nullable();
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
