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
        Schema::create('unidades', function (Blueprint $table) {
            $table->id();

            // Relación jerárquica (unidad superior)
            $table->foreignId('unidad_padre_id')
                ->nullable()
                ->constrained('unidades')
                ->nullOnDelete();

            // Jefe o responsable de esta unidad
            $table->string('jefe')->nullable();

            $table->string('nombre');
            $table->string('codigo', 20)->nullable();
            $table->integer('telefono');
            $table->integer('celular');

            $table->integer('nivel')->default(1);

            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }

};
