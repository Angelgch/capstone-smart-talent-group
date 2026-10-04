<?php

// database/migrations/2026_10_02_000003_create_documents_table.php
// TABLA 5 de 5: ARCHIVOS. Todos juntos: los que sube el cliente y los informes que sube el admin.
// Si el usuario elige "No enviar documento" NO hay fila (queda null).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('request_id')->constrained('requests')->cascadeOnDelete();   // a qué solicitud pertenece
            // A qué servicio pertenece (null = documento general de la solicitud)
            $table->foreignId('request_service_id')->nullable()->constrained('request_services')->cascadeOnDelete();
            // Quién lo subió físicamente (el cliente o el admin)
            $table->foreignId('user_id')->constrained()->restrictOnDelete();

            // requisito_cliente = lo sube el usuario al postular | informe_admin = PDF final del admin
            $table->enum('type', ['requisito_cliente', 'informe_admin']);
            $table->string('file_path');       // ruta dentro de storage (la BD guarda la ruta, no el archivo)
            $table->string('original_name');   // nombre que ve el usuario al descargar

            $table->timestamps();

            // Máximo 1 requisito y 1 informe por servicio
            $table->unique(['request_service_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
