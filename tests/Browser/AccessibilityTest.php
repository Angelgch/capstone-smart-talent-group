<?php

// tests/Browser/AccessibilityTest.php — Parte K de la guía: panel de accesibilidad

namespace Tests\Browser;

use Laravel\Dusk\Browser;

class AccessibilityTest extends SmartTalentTestCase
{
    public function test_alto_contraste_se_activa_y_se_recuerda(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs($this->user())->visit('/user/dashboard')
              ->click('#btnAccessibility')->waitFor('#a11yWidget .dropdown-menu.show')->step()
              ->click('[data-a11y="contrast"]')->step();

            $this->assertTrue($b->script("return document.documentElement.classList.contains('a11y-contrast')")[0]);

            // Al recargar, la preferencia sigue activa
            $b->refresh();
            $this->assertTrue($b->script("return document.documentElement.classList.contains('a11y-contrast')")[0]);
        });
    }

    public function test_aumentar_texto(): void
    {
        $this->browse(function (Browser $b) {
            $b->loginAs($this->user())->visit('/user/dashboard')
              ->click('#btnAccessibility')->waitFor('#a11yWidget .dropdown-menu.show')
              ->click('[data-a11y="size-up"]')->step();

            $this->assertSame('110%', $b->script("return document.documentElement.style.fontSize")[0]);
        });
    }
}