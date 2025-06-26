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
        Schema::create('modelos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_id')->constrained('marcas')->onDelete('cascade');
            $table->string('nombre'); 
            $table->year('anio_inicio')->nullable(); 
            $table->year('anio_fin')->nullable();
            $table->foreignId('tipo_vehiculo_id')->nullable()->constrained('tipos_vehiculos')->onDelete('set null');
            $table->timestamps();
            $table->unique(['marca_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelos');
    }
};