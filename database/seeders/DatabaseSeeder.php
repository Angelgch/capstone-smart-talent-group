<?php

// database/seeders/DatabaseSeeder.php   (REEMPLAZA el anterior)
// Crea las empresas, el admin único, un usuario de prueba y solicitudes de ejemplo.
// Se puede correr varias veces sin duplicar.

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Empresas (los usuarios dependen de ellas)
        $this->call(CompanySeeder::class);

        // 2) El ÚNICO admin del sistema: sin empresa (company_id = null).
        //    forceFill porque "role" no es asignable en masa (ver User.php)
        User::firstOrNew(['email' => 'admin@gmail.com'])->forceFill([
            'names'             => 'Administrador',
            'surnames'          => 'General',
            'password'          => '12345',          // se encripta sola (cast "hashed")
            'role'              => 'admin',
            'terms_accepted_at' => now(),
        ])->save();

        // 3) Usuario de prueba de Petro Perú (el mismo del login demo)
        User::firstOrNew(['email' => 'user@gmail.com'])->forceFill([
            'company_id'        => Company::where('ruc', '20100047218')->value('id'),
            'dni'               => '70000000',
            'names'             => 'Usuario',
            'surnames'          => 'Demo',
            'phone'             => '999999999',
            'password'          => '12345',
            'role'              => 'user',
            'terms_accepted_at' => now(),
        ])->save();

        // 4) Solicitudes de ejemplo por empresa (va al final: necesita a los usuarios)
        //$this->call(DemoRequestSeeder::class); Recien comentado por la rama prueba
    }
}
