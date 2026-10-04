<?php

// database/migrations/2026_10_02_000001_create_requests_table.php
// TABLA 3 de 5: SOLICITUDES (la "cabecera"): un candidato que un usuario manda a verificar.
// Modelo: VerificationRequest (se llama así para no chocar con Illuminate\Http\Request).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id(); // = N° de solicitud (se muestra como SOL-00001)

            // Responsable: el usuario que la registró. No se puede borrar un usuario con solicitudes.
            $table->foreignId('user_id')->constrained()->restrictOnDelete();

            // Datos del candidato
            $table->string('dni', 8)->index();   // NO único: el mismo DNI puede solicitarse otra vez
            $table->string('names');
            $table->string('surnames');
            $table->string('email');
            $table->string('phone', 15);
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
        Schema::dropIfExists('requests');
    }
};
