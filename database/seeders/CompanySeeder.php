<?php

// database/seeders/CompanySeeder.php
// Empresas de ejemplo con RUC, razón social y nombre comercial.
// (Los usuarios solo pueden registrarse con el RUC de una empresa que exista aquí.)

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['ruc' => '20100047218', 'trade_name' => 'Petro Perú', 'legal_name' => 'Petróleos del Perú - Petroperú S.A.'],
            ['ruc' => '20452430338', 'trade_name' => 'Claro Perú', 'legal_name' => 'América Móvil Perú S.A.C.'],
            ['ruc' => '20100113610', 'trade_name' => 'Backus',     'legal_name' => 'Unión de Cervecerías Peruanas Backus y Johnston S.A.A.'],
            ['ruc' => '20100053455', 'trade_name' => 'Interbank',  'legal_name' => 'Banco Internacional del Perú S.A.A. - Interbank'],
            ['ruc' => '20100055237', 'trade_name' => 'Alicorp',    'legal_name' => 'Alicorp S.A.A.'],
        ];

        // updateOrCreate por RUC: se puede correr varias veces sin duplicar
        foreach ($companies as $c) {
            Company::updateOrCreate(['ruc' => $c['ruc']], $c);
        }
    }
}
