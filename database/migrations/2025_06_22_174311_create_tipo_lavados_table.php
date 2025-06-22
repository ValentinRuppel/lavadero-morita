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
        Schema::create('tipo_lavados', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_lavado')->unique();
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 8, 2); // 8 dígitos en total, 2 después del punto decimal
            $table->integer('duracion_estimada')->comment('Duración en minutos'); // Duración en minutos
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_lavados');
    }
};