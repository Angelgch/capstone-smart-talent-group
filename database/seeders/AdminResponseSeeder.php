<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\VerificationRequest;
use App\Models\RequestService;

class AdminResponseSeeder extends Seeder
{
    public function run(): void
    {
        // Buscamos un usuario de rol 'user' o creamos uno si no existe
        $user = User::where('role', 'user')->first() ?? User::factory()->create([
            'role' => 'user'
        ]);

        // Creamos la solicitud usando la estructura exacta del modelo VerificationRequest
        $solicitud = VerificationRequest::create([
            'user_id' => $user->id,
            'dni' => '12345678',
            'names' => 'Zulema',
            'surnames' => 'Prueba',
            'email' => 'zulema@correo.com',
            'phone' => '987654321',
            'status' => 'en_espera'
        ]);

        // Creamos el servicio asociado utilizando 'request_id' que exige la relación HasMany
        RequestService::create([
            'request_id' => $solicitud->id,
            'service' => 'crediticias',
            'status' => 'en_espera'
        ]);
    }
}