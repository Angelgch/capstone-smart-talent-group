<?php

// tests/Browser/SmartTalentTestCase.php
// Base de todas las pruebas: BD limpia + datos base (empresas, admin y user@gmail.com) en CADA prueba,
// y ayudas para crear datos rápido sin llenar formularios.

namespace Tests\Browser;

use App\Models\Company;
use App\Models\User;
use App\Models\VerificationRequest;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

abstract class SmartTalentTestCase extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);   // empresas + admin@gmail.com + user@gmail.com (clave 12345)

        // ->step() = pausa entre acciones para VER lo que hace el navegador.
        // Se controla con DUSK_PACE (milisegundos) en .env.dusk.local. 0 = a toda velocidad.
        Browser::macro('step', function () {
            $this->pause((int) env('DUSK_PACE', 0));
            return $this;
        });
    }

    protected function admin(): User
    {
        return User::where('email', 'admin@gmail.com')->firstOrFail();
    }

    protected function user(): User
    {
        return User::where('email', 'user@gmail.com')->firstOrFail();   // pertenece a Petro Perú
    }

    protected function company(): Company
    {
        return Company::where('ruc', '20100047218')->firstOrFail();      // Petro Perú
    }

    // Crea una solicitud directo en la BD (más rápido que llenar el formulario)
    protected function makeRequest(User $owner, array $attrs = [], array $services = ['crediticias']): VerificationRequest
    {
        $request = VerificationRequest::create(array_merge([
            'user_id'  => $owner->id,
            'dni'      => '45612378',
            'names'    => 'Ana',
            'surnames' => 'Prueba',
            'email'    => 'ana@correo.com',
            'phone'    => '987654321',
        ], $attrs));

        foreach ($services as $service) {
            $request->services()->create(['service' => $service]);
        }

        return $request;
    }

    // PDF mínimo de prueba (la validación es por extensión)
    protected function samplePdf(): string
    {
        $dir = storage_path('app/dusk-fixtures');
        if (! is_dir($dir)) mkdir($dir, 0777, true);

        $path = $dir . DIRECTORY_SEPARATOR . 'ejemplo.pdf';
        file_put_contents($path, "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/Parent 2 0 R/MediaBox[0 0 200 200]>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF");

        return $path;
    }
}