<?php

// database/migrations/0001_01_01_000000_create_users_table.php   (REEMPLAZA la que trae Laravel)
// TABLA 2 de 5: USUARIOS del sistema. Cada usuario pertenece a una empresa (company_id);
// el admin general NO pertenece a ninguna (company_id = null).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Empresa del usuario. No se puede borrar una empresa que tenga usuarios.
            $table->foreignId('company_id')->nullable()->constrained()->restrictOnDelete();

            // dni y phone son nullable SOLO a nivel de BD (el admin no los tiene);
            // en el registro de usuarios son OBLIGATORIOS (los valida AuthController).
            $table->string('dni', 8)->nullable()->unique();
            $table->string('names', 100);
            $table->string('surnames', 100);
            $table->string('email')->unique();
            $table->string('phone', 15)->nullable();
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
