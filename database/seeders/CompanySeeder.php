<?php

// database/seeders/CompanySeeder.php
// Empresas de ejemplo (salen de DemoData::companies()) para la pantalla "Gestión de Empresas" del admin.

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['name' => 'Petro Perú', 'ruc' => '20100047218'],
            ['name' => 'Claro Perú', 'ruc' => '20452430338'],
            ['name' => 'Backus',     'ruc' => '20100113610'],
            ['name' => 'Interbank',  'ruc' => '20100053455'],
            ['name' => 'Alicorp',    'ruc' => '20100055237'],
        ];

        // updateOrCreate por RUC: se puede correr varias veces sin duplicar
        foreach ($companies as $c) {
            Company::updateOrCreate(['ruc' => $c['ruc']], ['name' => $c['name']]);
        }
    }
}