<?php

// database/migrations/2026_10_02_000002_create_verification_requests_table.php  (REEMPLAZA la anterior)
// CABECERA de la solicitud: un candidato que el usuario manda a verificar.
// Lo que se ve en la tabla resumen (matriz): N°, fecha, responsable, candidato y estado general.
// Los servicios y la dirección/referencia viven en request_services (detalle).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id(); // = N° de solicitud (se muestra como SOL-00001)

            // Responsable: el usuario que la registró. No se puede borrar un usuario con solicitudes.
            $table->foreignId('user_id')->constrained()->restrictOnDelete();

            // Datos del candidato
            $table->string('dni', 8)->index();   // NO único: el mismo DNI puede solicitarse otra vez
            $table->string('names');
            $table->string('surnames');
            $table->string('email');
            $table->string('phone', 15);

            // Observaciones: solo texto
            $table->text('observations')->nullable();

            // Estado general: se recalcula a partir de sus servicios (VerificationRequest::refreshStatus)
            $table->enum('status', ['en_espera', 'en_progreso', 'realizado', 'cancelado'])
                  ->default('en_espera')->index();

            $table->timestamps(); // created_at = fecha de solicitud
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_requests');
    }
};