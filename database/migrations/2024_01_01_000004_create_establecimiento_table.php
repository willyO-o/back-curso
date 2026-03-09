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
        Schema::create('establecimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 250);
            $table->text('descripcion')->nullable();
            $table->string('direccion', 250)->nullable();
            $table->string('imagen')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email', 250)->nullable();
            $table->string('website', 250)->nullable();
            $table->string('horario_apertura')->nullable();
            $table->string('horario_cierre')->nullable();
            $table->string('latitud',150)->nullable();
            $table->string('longitud',150)->nullable();
            $table->enum('estado', ['ACTIVO', 'INACTIVO'])->default('ACTIVO');
            $table->foreignId('categoria_id')->constrained('categoria')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establecimiento');
    }
};
