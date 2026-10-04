<?php

// database/migrations/2026_10_02_000002_create_request_services_table.php
// TABLA 4 de 5: DETALLE de la solicitud. UNA FILA por cada ítem que el usuario marcó
// (si no lo marcó, NO existe la fila: no hay "no solicitado" ni booleanos).
// Ítems = servicios de verificación + "direccion" y "referencia" (ver App\Support\ServiceCatalog).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests')->cascadeOnDelete();

            // Clave del ítem: antecedentes_policiales, crediticias, ..., direccion, referencia
            $table->string('service', 30);

            // Estado INDEPENDIENTE de cada servicio
            $table->enum('status', ['en_espera', 'en_progreso', 'realizado', 'cancelado'])
                  ->default('en_espera')->index();

            // Texto específico del ítem (ej. la dirección escrita). Si el usuario eligió PDF queda null
            // y el archivo va en documents.
            $table->text('detail')->nullable();

            $table->timestamps();

            // Un ítem no se repite dentro de la misma solicitud
            $table->unique(['request_id', 'service']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_services');
    }
};
