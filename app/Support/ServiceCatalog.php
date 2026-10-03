<?php

// app/Support/ServiceCatalog.php
// Catálogo de lo que se puede pedir. La BD solo guarda la clave (request_services.service).
//   all()    = servicios de verificación (los que se marcan con check)
//   extras() = dirección y referencia domiciliaria (el usuario elige TEXTO o PDF)
// Observaciones no está aquí: es solo texto y vive en verification_requests.observations.

namespace App\Support;

class ServiceCatalog
{
    public static function all(): array
    {
        return [
            // "Antecedentes Nacionales" separado en 3, cada uno con su propio estado y PDF
            'antecedentes_policiales' => ['full' => 'Antecedentes Policiales', 'short' => 'Policiales', 'group' => 'Antecedentes Nacionales'],
            'antecedentes_judiciales' => ['full' => 'Antecedentes Judiciales', 'short' => 'Judiciales', 'group' => 'Antecedentes Nacionales'],
            'antecedentes_penales'    => ['full' => 'Antecedentes Penales',    'short' => 'Penales',    'group' => 'Antecedentes Nacionales'],

            'crediticias'   => ['full' => 'Verificaciones Crediticias',   'short' => 'Verif. Crediticias'],
            'laborales'     => ['full' => 'Verificaciones Laborales',     'short' => 'Verif. Laborales'],
            'record'        => ['full' => 'Récord Laboral',               'short' => 'Récord Laboral'],
            'academicas'    => ['full' => 'Verificaciones Académicas',    'short' => 'Verif. Académicas'],
            'domiciliarias' => ['full' => 'Verificaciones Domiciliarias', 'short' => 'Verif. Domiciliarias'],
            'reniec'        => ['full' => 'Ficha RENIEC',                 'short' => 'Ficha RENIEC'],
        ];
    }

    public static function extras(): array
    {
        return [
            'direccion'  => ['full' => 'Dirección domiciliaria',  'short' => 'Dirección'],
            'referencia' => ['full' => 'Referencia domiciliaria', 'short' => 'Referencia'],
        ];
    }

    // Claves de los servicios (para validar y para calcular el estado general)
    public static function keys(): array
    {
        return array_keys(self::all());
    }

    // Claves de TODO lo que puede guardarse en request_services
    public static function allKeys(): array
    {
        return array_merge(self::keys(), array_keys(self::extras()));
    }
}