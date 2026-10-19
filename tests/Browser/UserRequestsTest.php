<?php

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
              ->click('.service-row[data-service="crediticias"] .service-check')
              ->click('.service-row[data-service="crediticias"] .yn-btn[data-choice="si"]')
              ->attach('#doc_file_crediticias', $pdf)
              ->type('#address', 'Av. Prueba 123')->step()
              ->click('#btnSubmitRequest')
              ->waitForText('creada correctamente')->assertSee('Ana')->assertSee('Pendiente')->step();
        });
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
    }
    
    public function test_buscador_y_filtro_de_la_matriz(): void
    {
        $pdf = $this->samplePdf();

        $this->browse(function (Browser $b) use ($pdf) {
            // 1. Creamos a Zulema vía interfaz
            $b->loginAs($this->user())->visit('/user/requests/create')
              ->type('#dni', '11111111')->type('#names', 'Zulema')->type('#surnames', 'Quispe')
              ->type('#email', 'zulema@correo.com')->type('#phone', '987654321')
              ->click('.service-row[data-service="crediticias"] .service-check')
              ->click('.service-row[data-service="crediticias"] .yn-btn[data-choice="si"]')
              ->attach('#doc_file_crediticias', $pdf)
              ->type('#address', 'Av. Zulema 123')
              ->click('#btnSubmitRequest')
              ->waitForText('creada correctamente');

            // 2. Secuencia rápida y fluida: Buscador -> Limpiar -> Filtro por estado -> Limpieza final
            $b->visit('/user/requests')
              ->pause(600)
              ->assertSee('Zulema')
              
              // Búsqueda por texto y limpiar
              ->type('input[name=q]', 'Zulema')
              ->click('form.filter-section button[type="submit"]')
              ->assertSee('Zulema')
              ->clickLink('Limpiar')
              
              // Filtro rápido por estado (en espera) y limpieza final
              ->select('status', 'en_espera')
              ->click('form.filter-section button[type="submit"]')
              ->clickLink('Limpiar')
              ->step();
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
    }
}