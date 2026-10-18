<?php

namespace Tests\Unit;

use App\Support\StatusLabel;
use PHPUnit\Framework\TestCase;

class StatusLabelTest extends TestCase
{
    /**
     * Valida la conversión de estados generales a texto legible para el usuario.
     */
    public function test_general_status_label_conversion(): void
    {
        $this->assertSame('Pendiente', StatusLabel::general('en_espera'));
        $this->assertSame('En Proceso', StatusLabel::general('en_progreso'));
        $this->assertSame('Completado', StatusLabel::general('realizado'));
        $this->assertSame('Cancelado', StatusLabel::general('cancelado'));
        $this->assertNull(StatusLabel::general('invalido'));
        $this->assertNull(StatusLabel::general(null));
    }

    /**
     * Valida la conversión de estados por servicio individual en el detalle.
     */
    public function test_service_status_label_conversion(): void
    {
        $this->assertSame('En Espera', StatusLabel::service('en_espera'));
        $this->assertSame('En Proceso', StatusLabel::service('en_progreso'));
        $this->assertSame('Realizado', StatusLabel::service('realizado'));
        $this->assertSame('Cancelado', StatusLabel::service('cancelado'));
        $this->assertNull(StatusLabel::service('no_solicitado'));
        $this->assertNull(StatusLabel::service(null));
    }

    /**
     * Valida la semaforización de estados asociando el estado a su clase CSS/color correspondiente.
     */
    public function test_status_semaphorization_to_css_badge_classes(): void
    {
        $this->assertSame('dash-badge-pendiente', StatusLabel::badgeClass('en_espera'));
        $this->assertSame('dash-badge-progreso', StatusLabel::badgeClass('en_progreso'));
        $this->assertSame('dash-badge-completado', StatusLabel::badgeClass('realizado'));
        $this->assertSame('dash-badge-cancelado', StatusLabel::badgeClass('cancelado'));
        $this->assertNull(StatusLabel::badgeClass('desconocido'));
        $this->assertNull(StatusLabel::badgeClass(null));
    }
}

