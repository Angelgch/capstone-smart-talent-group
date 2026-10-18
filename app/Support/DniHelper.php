<?php

// app/Support/DniHelper.php
// Sanitización y validación aislada de formato DNI (estándar peruano de 8 dígitos).

namespace App\Support;

class DniHelper
{
    /**
     * Sanitiza el DNI eliminando espacios en blanco y cualquier carácter no numérico.
     */
    public static function sanitize(?string $dni): string
    {
        if ($dni === null) {
            return '';
        }

        return preg_replace('/\D/', '', trim($dni));
    }

    /**
     * Valida que el DNI cumpla estrictamente con el formato de 8 dígitos numéricos.
     */
    public static function isValid(?string $dni): bool
    {
        $sanitized = self::sanitize($dni);

        return (bool) preg_match('/^[0-9]{8}$/', $sanitized);
    }
}

