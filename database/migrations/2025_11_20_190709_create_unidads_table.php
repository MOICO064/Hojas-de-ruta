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
            $table->foreignId('unidad_padre_id')
                ->nullable()
                ->constrained('unidades')
                ->nullOnDelete();

            $table->string('jefe')->nullable();

            $table->string('nombre');
            $table->string('codigo', 20)->nullable();
            $table->integer('telefono');
            $table->integer('interno');

            $table->integer('nivel')->default(1);

            $table->integer('numero_unidad_actual')->default(0);
            $table->enum('estado', ['ACTIVO', 'ANULADO'])->default('ACTIVO');

            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }

};
