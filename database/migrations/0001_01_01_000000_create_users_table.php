<?php

// database/migrations/0001_01_01_000000_create_users_table.php
// REEMPLAZA la migración de usuarios que trae Laravel (proyecto nuevo: todavía no hay datos).
//
// users = Tabla `users` del Prompt Maestro:
//   dni, names, surnames, email (único), ruc, phone, password, role (admin | user)
// + terms_accepted_at: cuándo aceptó los términos y condiciones en el registro.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // dni, ruc y phone son nullable SOLO a nivel de BD, porque el admin no los tiene.
            // En el registro de usuarios son OBLIGATORIOS (se validan en el backend).
            $table->string('dni', 8)->nullable()->unique();   // 8 dígitos, solo números
            $table->string('names', 100);
            $table->string('surnames', 100);
            $table->string('email')->unique();
            $table->string('ruc', 11)->nullable()->index();   // libre y NO único: varios usuarios pueden compartir RUC
            $table->string('phone', 15)->nullable();          // el registro exige 9 dígitos; 15 queda listo para otros países
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');

            $table->timestamp('terms_accepted_at')->nullable();
            $table->timestamp('email_verified_at')->nullable(); // lo usa Laravel; por ahora no lo usamos
            $table->rememberToken();
            $table->timestamps();
        });

        // Tablas estándar de Laravel (recuperar contraseña y sesiones en BD)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};