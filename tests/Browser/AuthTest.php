<?php

// tests/Browser/AuthTest.php — Parte A de la guía: acceso y registro

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class AuthTest extends SmartTalentTestCase
{
    public function test_admin_entra_a_su_dashboard(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')
              ->type('#loginEmail', 'admin@gmail.com')->type('#loginPassword', '12345')->step()
              ->click('#loginForm button[type=submit]')
              ->waitForLocation('/admin/dashboard')->assertPathIs('/admin/dashboard')->step();
        });
    }

    public function test_usuario_entra_a_su_dashboard(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')
              ->type('#loginEmail', 'user@gmail.com')->type('#loginPassword', '12345')->step()
              ->click('#loginForm button[type=submit]')
              ->waitForLocation('/user/dashboard')->assertPathIs('/user/dashboard')->step();
        });
    }

    public function test_contrasena_incorrecta_muestra_error(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/')
              ->type('#loginEmail', 'user@gmail.com')->type('#loginPassword', 'incorrecta')->step()
              ->click('#loginForm button[type=submit]')
              ->waitForText('Credenciales incorrectas')->assertPathIs('/')->step();
        });
    }

    public function test_registro_con_ruc_inexistente_es_rechazado(): void
    {
        $this->browse(function (Browser $b) {
            $this->fillRegister($b, ['ruc' => '99999999999'])
                 ->click('#btnRegister')
                 ->waitForText('No hay una empresa registrada con ese RUC')->step();
        });

        $this->assertDatabaseMissing('users', ['email' => 'nuevo@correo.com']);
    }

    public function test_registro_correcto_entra_al_panel_del_usuario(): void
    {
        $this->browse(function (Browser $b) {
            $this->fillRegister($b, ['ruc' => '20452430338'])   // Claro Perú
                 ->click('#btnRegister')
                 ->waitForLocation('/user/dashboard', 10)->assertPathIs('/user/dashboard')->step();
        });

        // Queda como usuario (no admin) y asociado a la empresa del RUC
        $this->assertDatabaseHas('users', ['email' => 'nuevo@correo.com', 'role' => 'user']);
    }

    // Llena el formulario de registro. El botón "Registrarse" solo se activa al marcar los términos.
    private function fillRegister(Browser $b, array $override = []): Browser
    {
        $d = array_merge([
            'ruc' => '20452430338', 'dni' => '45678912', 'names' => 'Nuevo', 'surnames' => 'Usuario',
            'email' => 'nuevo@correo.com', 'phone' => '987654321', 'password' => 'clave12345',
        ], $override);

        $b->visit('/')->click('#btnTabReg')->waitFor('#registerForm')
          ->type('#regRuc', $d['ruc'])->type('#regDni', $d['dni'])
          ->type('#regNames', $d['names'])->type('#regSurnames', $d['surnames'])
          ->type('#regEmail', $d['email'])->type('#regPhone', $d['phone'])
          ->type('#regPassword', $d['password'])->type('#regPasswordConfirm', $d['password'])
          ->assertButtonDisabled('#btnRegister')->check('#regTerms')->step();

        return $b;
    }
}