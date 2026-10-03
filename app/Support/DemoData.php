<?php

namespace App\Support;

// OJO: todavía lo usan 8 rutas de web.php (dashboards, solicitudes, matriz del admin).
// Se borra al FINAL, cuando todas lean de la BD. services() ya no se usa (ahora es ServiceCatalog).

class DemoData
{
    // Un servicio que NO aparece en 'services' de un candidato = No Solicitado
    public static function services(): array
    {
        return [
            'antecedentes'  => ['full' => 'Antecedentes Nacionales',       'short' => 'Antecedentes'],
            'crediticias'   => ['full' => 'Verificaciones Crediticias',    'short' => 'Verif. Crediticias'],
            'laborales'     => ['full' => 'Verificaciones Laborales',      'short' => 'Verif. Laborales'],
            'record'        => ['full' => 'Récord Laboral',                'short' => 'Récord Laboral'],
            'academicas'    => ['full' => 'Verificaciones Académicas',     'short' => 'Verif. Académicas'],
            'domiciliarias' => ['full' => 'Verificaciones Domiciliarias',  'short' => 'Verif. Domiciliarias'],
            'reniec'        => ['full' => 'Ficha RENIEC',                  'short' => 'Ficha RENIEC'],
        ];
    }

    public static function companies(): array
    {
        return [
            ['id' => 1, 'name' => 'Petro Perú', 'ruc' => '20100047218'],
            ['id' => 2, 'name' => 'Claro Perú', 'ruc' => '20452430338'],
            ['id' => 3, 'name' => 'Backus',     'ruc' => '20100113610'],
            ['id' => 4, 'name' => 'Interbank',  'ruc' => '20100053455'],
            ['id' => 5, 'name' => 'Alicorp',    'ruc' => '20100055237'],
        ];
    }

    public static function candidates(): array
    {
        return [
            [
                'dni' => '72830595', 'name' => 'Wendy Esteferi Quice Cedeñeda', 'status' => 'Realizado',
                'services' => [
                    'antecedentes' => 'Realizado', 'crediticias' => 'Realizado',
                    'laborales' => 'Cancelado', 'record' => 'Realizado', 'academicas' => 'Realizado',
                ],
                'files' => [
                    'antecedentes' => '72830595_antecedentes.pdf',
                    'crediticias'  => '72830595_crediticias.pdf',
                    'record'       => '72830595_record_laboral.pdf',
                    'academicas'   => '72830595_academicas.pdf',
                    'documents' => ['academicas' => '72830595_certificado_estudios.pdf'],
                ],
                'direccion' => 'Av. Bertolotto 752, San Miguel', 'referencia' => '', 'observaciones' => 'Validar estudios universitarios',
            ],
            [
                'dni' => '75953952', 'name' => 'Heiter David Alvarez Paredes', 'status' => 'Cancelado',
                'services' => [
                    'antecedentes' => 'Cancelado', 'crediticias' => 'Realizado',
                    'laborales' => 'Cancelado', 'record' => 'En Proceso', 'academicas' => 'En Proceso',
                ],
                'files' => ['crediticias' => '75953952_crediticias.pdf'],
                'direccion' => '', 'referencia' => '', 'observaciones' => '',
            ],
            [
                'dni' => '71055901', 'name' => 'Elizabeth Yajaira Livia Flores', 'status' => 'En Proceso',
                'services' => [
                    'antecedentes' => 'Realizado', 'crediticias' => 'Realizado', 'laborales' => 'En Proceso',
                    'record' => 'Realizado', 'academicas' => 'Realizado', 'domiciliarias' => 'Realizado', 'reniec' => 'Realizado',
                ],
                'files' => [
                    'antecedentes'  => '71055901_antecedentes.pdf',
                    'crediticias'   => '71055901_crediticias.pdf',
                    'record'        => '71055901_record_laboral.pdf',
                    'academicas'    => '71055901_academicas.pdf',
                    'domiciliarias' => '71055901_domiciliarias.pdf',
                    'reniec'        => '71055901_reniec.pdf',
                ],
                'direccion' => 'Jr. Turín 103, San Martín de Porres', 'referencia' => 'Frente al parque', 'observaciones' => '',
            ],
        ];
    }

    public static function company($id): array
    {
        foreach (self::companies() as $c) {
            if ((int) $c['id'] === (int) $id) return $c;
        }
        abort(404);
    }

    public static function candidate(string $dni): array
    {
        foreach (self::candidates() as $c) {
            if ($c['dni'] === $dni) return $c;
        }
        abort(404);
    }
    public static function requests(): array
    {
        return [
            ['company_id' => 1, 'ruc' => '20100047218', 'name' => 'Petro Perú', 'docs' => ['Antecedentes', 'Verif. Laborales', 'Récord Laboral'], 'date' => '2026-09-27', 'status' => 'Pendiente',   'assigned' => null],
            ['company_id' => 2, 'ruc' => '20452430338', 'name' => 'Claro Perú', 'docs' => ['Verif. Crediticias', 'Ficha RENIEC'],                   'date' => '2026-09-26', 'status' => 'En Progreso', 'assigned' => 'asesor1@gmail.com'],
            ['company_id' => 3, 'ruc' => '20100113610', 'name' => 'Backus',     'docs' => ['Antecedentes', 'Verif. Académicas'],                    'date' => '2026-09-25', 'status' => 'Completado',  'assigned' => 'asesor2@gmail.com'],
            ['company_id' => 4, 'ruc' => '20100053455', 'name' => 'Interbank',  'docs' => ['Verif. Domiciliarias'],                                 'date' => '2026-09-24', 'status' => 'Cancelado',   'assigned' => 'asesor1@gmail.com'],
            ['company_id' => 5, 'ruc' => '20100055237', 'name' => 'Alicorp',    'docs' => ['Antecedentes', 'Verif. Laborales', 'Verif. Académicas', 'Ficha RENIEC'], 'date' => '2026-09-23', 'status' => 'En Progreso', 'assigned' => 'asesor3@gmail.com'],
            ['company_id' => 1, 'ruc' => '20100047218', 'name' => 'Petro Perú', 'docs' => ['Récord Laboral'],                                       'date' => '2026-09-20', 'status' => 'Completado',  'assigned' => 'asesor2@gmail.com'],
            ['company_id' => 2, 'ruc' => '20452430338', 'name' => 'Claro Perú', 'docs' => ['Antecedentes'],                                         'date' => '2026-09-18', 'status' => 'Completado',  'assigned' => 'asesor1@gmail.com'],
        ];
    }
    public static function userRequests(): array
{
    $dates = ['2026-09-26', '2026-09-24', '2026-09-20'];
    $out = [];

    foreach (self::candidates() as $i => $c) {
        $c['date'] = $dates[$i] ?? '2026-09-01';
        $out[] = $c;
    }

    return $out;
}
}