<?php

// tests/Browser/AdminCompaniesTest.php — Parte B de la guía: empresas del admin

namespace Tests\Browser;

use App\Models\Company;
use Laravel\Dusk\Browser;

class AdminCompaniesTest extends SmartTalentTestCase
{
    public function test_admin_registra_una_empresa_y_rechaza_ruc_repetido(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs($this->admin())->visit('/admin/companies')
              ->clickLink('Nueva empresa')->waitFor('#ruc')
              ->type('#ruc', '20555666777')->type('#legal_name', 'Empresa de Prueba S.A.C.')
              ->type('#trade_name', 'Prueba Dusk')->step()
              ->press('Registrar empresa')
              ->assertSee('registrada correctamente')->assertSee('Prueba Dusk')->step();

            // Segundo intento con el MISMO RUC (es el de Petro Perú del seeder)
            $b->clickLink('Nueva empresa')->waitFor('#ruc')
              ->type('#ruc', '20100047218')->type('#legal_name', 'Otra S.A.')->type('#trade_name', 'Otra')->step()
              ->press('Registrar empresa')
              ->assertSee('Ya existe una empresa registrada con ese RUC')->step();
        });

        $this->assertDatabaseHas('companies', ['ruc' => '20555666777']);
    }

    public function test_admin_edita_el_nombre_comercial(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs($this->admin())->visit('/admin/companies/' . $this->company()->id . '/edit')
              ->type('#trade_name', 'Petro Renombrada')->step()
              ->press('Guardar cambios')
              ->assertSee('actualizada correctamente')->assertSee('Petro Renombrada')->step();
        });
    }

    public function test_eliminar_empresa_pide_la_contrasena_del_admin(): void
    {
        $empresa = Company::create(['ruc' => '20999888777', 'legal_name' => 'Borrar S.A.', 'trade_name' => 'Para Borrar']);

        $this->browse(function (Browser $b) use ($empresa) {
            $b->loginAs($this->admin())->visit('/admin/companies/' . $empresa->id . '/edit')
              ->press('Eliminar empresa')->waitFor('#deleteCompanyPassword')
              ->type('#deleteCompanyPassword', 'contraseña-mala')->step()
              ->press('Sí, eliminar')
              ->assertSee('La contraseña es incorrecta')->step();

            $this->assertDatabaseHas('companies', ['ruc' => '20999888777']);   // no se borró

            $b->press('Eliminar empresa')->waitFor('#deleteCompanyPassword')
              ->type('#deleteCompanyPassword', '12345')->step()
              ->press('Sí, eliminar')
              ->assertPathIs('/admin/companies')->assertSee('eliminada')->step();
        });

        $this->assertDatabaseMissing('companies', ['ruc' => '20999888777']);
    }
}