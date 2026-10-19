<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use App\Models\VerificationRequest;

class AdminResponseTest extends SmartTalentTestCase
{
    public function test_admin_no_puede_marcar_realizado_sin_informe_y_el_usuario_ve_el_informe(): void
    {
        // Bloque 1: Obtener la última solicitud generada, calcular la compañía y preparar el PDF de prueba
        $solicitud = VerificationRequest::latest('id')->first();
        $companyId = $solicitud->user->company_id ?? $this->company()->id;
        $url = route('admin.companies.requests.show', [$companyId, $solicitud]);
        $pdf = $this->samplePdf();

        $this->browse(function (Browser $b) use ($url, $pdf, $solicitud) {
            
            // Bloque 2: Inicio de sesión del administrador y acceso a la ruta de la solicitud
            $b->loginAs($this->admin())
              ->visit($url)
              ->pause(800);

            // Bloque 3: Desplazamiento hacia abajo (scroll) para visualizar la tabla completa de servicios
            $b->script('window.scrollTo(0, document.body.scrollHeight);');
            $b->pause(500);

            // Bloque 4: Validación de que el administrador no puede marcar "Realizado" sin adjuntar un informe previo
            $b->select('select[name="status[crediticias]"]', 'realizado')
              ->pause(300)
              ->click('#btnSaveAdmin')
              ->waitForText('sube primero el informe PDF')->step();

            // Bloque 5: Envío exitoso en la misma página (SIN recargar para no perder el DOM)
            $b->script('window.scrollTo(0, document.body.scrollHeight);');
            $b->pause(500);

            $b->select('select[name="status[crediticias]"]', 'realizado')
              ->pause(300)
              ->attach('input[name="informe[crediticias]"]', $pdf)
              ->pause(1000)
              ->click('#btnSaveAdmin')
              ->waitForText('Cambios guardados correctamente')
              ->pause(1000)->step();

            // Bloque 6: Cambio de sesión al usuario cliente, visita a su sección de descargas para ver el archivo y paso al dashboard
            $b->logout()->loginAs($this->user())
              ->visit('/user/requests/' . $solicitud->id . '/downloads')
              ->assertSee('ejemplo.pdf')
              ->pause(500)
              ->visit('/user/dashboard')
              ->assertSee($solicitud->dni)->step();
        });

        // Bloque 7: Verificación final validada de forma integral por el recorrido del navegador
        $this->assertTrue(true);
    }
}