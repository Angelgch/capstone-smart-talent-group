<?php

// database/migrations/2026_10_02_000004_create_documents_table.php
// Archivos de cada servicio. Si el usuario elige "No enviar documento" NO hay fila (queda null).
//   kind = adjunto   -> lo sube el usuario al crear/editar
//   kind = resultado -> PDF oficial que devuelve el admin

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_service_id')->constrained()->cascadeOnDelete();

            $table->enum('kind', ['adjunto', 'resultado']);
            $table->string('file_path');       // ruta dentro de storage (la BD guarda la ruta, no el archivo)
            $table->string('original_name');   // nombre que ve el usuario al descargar

            $table->timestamps();

            // Máximo 1 adjunto y 1 resultado por servicio
            $table->unique(['request_service_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};