<?php

// database/migrations/0001_01_01_000000_create_companies_table.php
// TABLA 1 de 5: EMPRESAS clientes. Va PRIMERO porque users depende de ella (company_id).
// (El nombre empieza igual que el de users y, por orden alfabético, "companies" corre antes que "users".)

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11)->unique();   // 11 dígitos, no se repite entre empresas
            $table->string('legal_name');          // Razón social
            $table->string('trade_name');          // Nombre comercial (el que se muestra en pantalla)
            $table->string('address')->nullable();
            $table->string('phone', 15)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
