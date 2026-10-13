<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class SecurityInjectionTest extends TestCase
{
    use RefreshDatabase; // <- Esto crea las tablas automáticamente en memoria

    public function test_trabajador_no_puede_entrar_a_panel_de_admin()
    {
        // Creamos un usuario de prueba directamente con los campos de nuestra BD
        $worker = User::create([
            'names' => 'Juan',
            'surnames' => 'Perez',
            'email' => 'worker@test.com',
            'password' => bcrypt('password'),
            'role' => 'company_worker',
            'status' => 'active',
        ]);

        // Intentamos entrar a una ruta protegida de administrador
        $response = $this->actingAs($worker)->get('/admin/dashboard');

        // El sistema debe denegar el acceso (403 Forbidden)
        $response->assertStatus(403);
    }
}