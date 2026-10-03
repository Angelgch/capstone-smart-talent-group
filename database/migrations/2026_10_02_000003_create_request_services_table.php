<?php

// database/migrations/2026_10_02_000003_create_request_services_table.php  (REEMPLAZA la anterior)
// DETALLE de la solicitud: una fila por cada ítem pedido (candidato x ítem).
// Ítems = los servicios de verificación + "direccion" y "referencia" (ver App\Support\ServiceCatalog).
// Si el usuario NO marca un ítem, simplemente NO se crea la fila (= no solicitado).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('verification_request_id')->constrained()->cascadeOnDelete();

            // Clave del ítem: antecedentes_policiales, crediticias, ..., direccion, referencia.
            // Los textos bonitos salen de App\Support\ServiceCatalog.
            $table->string('service', 30);

            // Arranca en "en_espera" al marcar el check; el admin lo cambia después
            $table->enum('status', ['en_espera', 'en_progreso', 'realizado', 'cancelado'])
                  ->default('en_espera')->index();

            // Solo para direccion / referencia cuando el usuario elige escribir TEXTO.
            // Si elige PDF, este campo queda null y el archivo va en documents.
            $table->text('text')->nullable();

            $table->timestamps();

            // Un ítem no se repite dentro de la misma solicitud
            $table->unique(['verification_request_id', 'service']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_services');
    }
};