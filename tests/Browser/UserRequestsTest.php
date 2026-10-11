<?php

// tests/Browser/UserRequestsTest.php — Partes C, D y H de la guía: crear, buscar y eliminar solicitudes

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class UserRequestsTest extends SmartTalentTestCase
{
    public function test_usuario_crea_una_solicitud_con_documento(): void
    {
        $pdf = $this->samplePdf();

        $this->browse(function (Browser $b) use ($pdf) {
            $b->loginAs($this->user())->visit('/user/requests/create')
              ->type('#dni', '45612378')->type('#names', 'Ana')->type('#surnames', 'Prueba Dusk')
              ->type('#email', 'ana@correo.com')->type('#phone', '987654321')->step()
              // Marca el servicio (se hace clic en la etiqueta: el checkbox está oculto), elige "Sí" y adjunta el PDF
              ->click('.service-row[data-service="crediticias"] .service-check')
              ->click('.service-row[data-service="crediticias"] .yn-btn[data-choice="si"]')
              ->attach('#doc_file_crediticias', $pdf)
              ->type('#address', 'Av. Prueba 123')->step()
              ->click('#btnSubmitRequest')
              ->waitForText('creada correctamente')->assertSee('Ana')->assertSee('Pendiente')->step();
        });

        $this->assertDatabaseHas('requests', ['dni' => '45612378', 'address' => 'Av. Prueba 123']);
        $this->assertDatabaseHas('request_services', ['service' => 'crediticias']);
        $this->assertDatabaseHas('documents', ['type' => 'requisito_cliente', 'original_name' => 'ejemplo.pdf']);
    }

    public function test_no_deja_enviar_sin_servicios(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs($this->user())->visit('/user/requests/create')
              ->type('#dni', '45612378')->type('#names', 'Ana')->type('#surnames', 'Prueba')
              ->type('#email', 'ana@correo.com')->type('#phone', '987654321')->step()
              ->click('#btnSubmitRequest')
              ->waitForText('Seleccione al menos un servicio')->assertPathIs('/user/requests/create')->step();
        });

        $this->assertDatabaseCount('requests', 0);
    }

    public function test_buscador_y_filtro_de_la_matriz(): void
    {
        $this->makeRequest($this->user(), ['dni' => '11111111', 'names' => 'Zulema', 'surnames' => 'Quispe']);
        $this->makeRequest($this->user(), ['dni' => '22222222', 'names' => 'Bruno', 'surnames' => 'Rojas']);

        $this->browse(function (Browser $b) {
            $b->loginAs($this->user())->visit('/user/requests')
              ->assertSee('Zulema')->assertSee('Bruno')->step()
              ->type('input[name=q]', 'Zulema')->press('Filtrar')
              ->assertSee('Zulema')->assertDontSee('Bruno')->step()
              ->clickLink('Limpiar')->assertSee('Bruno')->step();
        });
    }

    public function test_eliminar_una_solicitud_pide_la_contrasena(): void
    {
        $solicitud = $this->makeRequest($this->user());

        $this->browse(function (Browser $b) {
            $b->loginAs($this->user())->visit('/user/requests')
              ->click('.btn-icon-delete')->waitFor('#deletePassword')
              ->type('#deletePassword', 'mala')->step()
              ->click('#deleteForm button[type=submit]')
              ->waitForText('La contraseña es incorrecta')->step();

            $b->click('.btn-icon-delete')->waitFor('#deletePassword')
              ->type('#deletePassword', '12345')->step()
              ->click('#deleteForm button[type=submit]')
              ->waitForText('eliminada')->step();
        });

        $this->assertDatabaseMissing('requests', ['id' => $solicitud->id]);
    }
}   