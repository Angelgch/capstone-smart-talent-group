<?php

// tests/Browser/SecurityTest.php — Parte 4 de la guía: permisos y datos ajenos

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;

class SecurityTest extends SmartTalentTestCase
{
    public function test_invitado_es_enviado_al_login(): void
    {
        $this->browse(function (Browser $b) {
            $b->visit('/user/requests')->assertPathIs('/')->step();
        });
    }

    public function test_usuario_no_puede_entrar_al_panel_del_admin(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs($this->user())->visit('/admin/companies')
              ->assertSee('No tienes permiso')->assertDontSee('Gestión de Empresas')->step();
        });
    }

    public function test_usuario_no_ve_la_solicitud_de_otro(): void
    {
        $otro = User::create([
            'company_id' => $this->company()->id, 'dni' => '70000099', 'names' => 'Otro', 'surnames' => 'Usuario',
            'email' => 'otro@correo.com', 'phone' => '999999999', 'password' => 'clave12345',
        ]);
        $ajena = $this->makeRequest($otro, ['names' => 'Candidato', 'surnames' => 'Ajeno']);

        $this->browse(function (Browser $b) use ($ajena) {
            $b->loginAs($this->user())->visit('/user/requests/' . $ajena->id)
              ->assertDontSee('Candidato Ajeno')->step()
              ->visit('/user/requests')->assertDontSee('Candidato')->step();
        });
    }
}