<?php

// tests/Browser/AdminResponseTest.php — Partes E y F de la guía: el admin responde y el usuario descarga

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class AdminResponseTest extends SmartTalentTestCase
{
    public function test_admin_no_puede_marcar_realizado_sin_informe_y_el_usuario_ve_el_informe(): void
    {
        $solicitud = $this->makeRequest($this->user());
        $url = '/admin/companies/' . $this->company()->id . '/requests/' . $solicitud->id;
        $pdf = $this->samplePdf();

        $this->browse(function (Browser $b) use ($url, $pdf, $solicitud) {
            // 1) "Realizado" sin informe => error
            $b->loginAs($this->admin())->visit($url)
              ->select('status[crediticias]', 'realizado')->step()
              ->click('#btnSaveAdmin')
              ->waitForText('sube primero el informe PDF')->step();

            // 2) Con el informe => se guarda
            $b->visit($url)
              ->select('status[crediticias]', 'realizado')
              ->attach('informe[crediticias]', $pdf)->step()
              ->click('#btnSaveAdmin')
              ->waitForText('Cambios guardados correctamente')->step();

            // 3) El usuario ve el informe en su página de Descargas
            $b->logout()->loginAs($this->user())
              ->visit('/user/requests/' . $solicitud->id . '/downloads')
              ->assertSee('ejemplo.pdf')->step();

            // 4) ...y la solicitud aparece en su dashboard
            $b->visit('/user/dashboard')->assertSee($solicitud->code)->step();
        });

        $this->assertDatabaseHas('request_services', ['service' => 'crediticias', 'status' => 'realizado']);
        $this->assertDatabaseHas('documents', ['type' => 'informe_admin']);
        $this->assertDatabaseHas('requests', ['id' => $solicitud->id, 'status' => 'realizado']);
    }
}