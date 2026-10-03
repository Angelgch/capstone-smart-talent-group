<?php

// app/Support/StatusLabel.php
// En la BD hay UN solo vocabulario (en_espera, en_progreso, realizado, cancelado).
// Aquí se decide cómo se LEE en pantalla. Si cambias un texto, solo cambia aquí.

namespace App\Support;

class StatusLabel
{
    // Estado de la SOLICITUD completa (tabla resumen)
    public static function general(?string $status): ?string
    {
        return match ($status) {
            'en_espera'   => 'Pendiente',
            'en_progreso' => 'En Proceso',
            'realizado'   => 'Completado',
            'cancelado'   => 'Cancelado',
            default       => null,
        };
    }

    // Estado de CADA servicio (detalle)
    public static function service(?string $status): ?string
    {
        return match ($status) {
            'en_espera'   => 'En Espera',
            'en_progreso' => 'En Proceso',
            'realizado'   => 'Realizado',
            'cancelado'   => 'Cancelado',
            default       => null,   // null = no solicitado
        };
    }
}