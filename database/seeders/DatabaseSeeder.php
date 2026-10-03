<?php

// database/seeders/DatabaseSeeder.php  (reemplaza el que trae Laravel)
// Crea el admin único, un usuario de prueba, las empresas y solicitudes de ejemplo. Se puede correr varias veces sin duplicar.

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Empresas de ejemplo (para el index de companies del admin)
        $this->call(CompanySeeder::class);

        // El ÚNICO admin del sistema. forceFill porque "role" no es asignable en masa (ver User.php)
        User::firstOrNew(['email' => 'admin@gmail.com'])->forceFill([
            'names'             => 'Administrador',
            'surnames'          => 'General',
            'password'          => '12345',          // se encripta sola (cast "hashed")
            'role'              => 'admin',
            'terms_accepted_at' => now(),
        ])->save();

        // Usuario de prueba (el mismo del login demo)
        User::firstOrNew(['email' => 'user@gmail.com'])->forceFill([
            'dni'               => '70000000',
            'names'             => 'Usuario',
            'surnames'          => 'Demo',
            'ruc'               => '20100047218',
            'phone'             => '999999999',
            'password'          => '12345',
            'role'              => 'user',
            'terms_accepted_at' => now(),
        ])->save();

        // Solicitudes de ejemplo por empresa (va al final: necesita que user@gmail.com ya exista)
        $this->call(DemoRequestSeeder::class);
    }
}