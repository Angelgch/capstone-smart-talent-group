<?php

// database/seeders/DemoRequestSeeder.php  (REEMPLAZA el anterior)
// Solicitudes de EJEMPLO (borrador) para ver la matriz de cada empresa con datos distintos.
// Petro Perú trae 3 candidatos; las otras 4 empresas, 1 cada una. Se puede correr varias veces sin duplicar.
// Los archivos son solo rutas de ejemplo (no existen en disco).

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Document;
use App\Models\RequestService;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Database\Seeder;

class DemoRequestSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->data() as $companyName => $d) {
            // Usuario de la empresa (company_id = esa empresa, así la matriz sabe de quién es cada solicitud)
            $user = User::firstOrNew(['email' => $d['email']]);
            if (! $user->exists) {
                $user->forceFill([
                    'company_id' => Company::where('trade_name', $companyName)->value('id'),
                    'names' => 'Usuario', 'surnames' => $companyName, 'dni' => $d['dni'], 'phone' => '999999999',
                    'password' => '12345', 'role' => 'user', 'terms_accepted_at' => now(),
                ])->save();
            }

            foreach ($d['requests'] as $r) {
                $this->createRequest($user, $r);
            }
        }
    }

    private function createRequest(User $user, array $r): void
    {
        // CABECERA
        $req = VerificationRequest::firstOrNew(['user_id' => $user->id, 'dni' => $r['dni']]);

        if (! $req->exists) {
            $req->forceFill([
                'names' => $r['names'], 'surnames' => $r['surnames'], 'email' => $r['email'], 'phone' => $r['phone'],
                'observations' => $r['observations'] ?? null,
                'created_at' => $r['date'] . ' 10:00:00', 'updated_at' => $r['date'] . ' 10:00:00',
            ])->save();
        }

        // DETALLE: un ítem pedido = una fila (lo que no aparece = no solicitado).
        // 'texts' = texto de direccion/referencia (columna detail) cuando eligió escribir en vez de subir PDF.
        foreach ($r['services'] as $key => $status) {
            RequestService::updateOrCreate(
                ['request_id' => $req->id, 'service' => $key],
                ['status' => $status, 'detail' => $r['texts'][$key] ?? null]
            );
        }

        // Documentos: informe del admin (informe_admin) y archivo del usuario (requisito_cliente)
        $adminId = User::where('role', 'admin')->value('id');
        $this->documents($req, $r['results'] ?? [], 'informe_admin', 'results/', $adminId);
        $this->documents($req, $r['attachments'] ?? [], 'requisito_cliente', 'attachments/', $req->user_id);

        $req->refreshStatus(); // estado general según sus servicios
    }

    private function documents(VerificationRequest $req, array $files, string $type, string $folder, int $uploaderId): void
    {
        foreach ($files as $key => $file) {
            $item = RequestService::where('request_id', $req->id)->where('service', $key)->first();
            if (! $item) continue;

            Document::updateOrCreate(
                ['request_service_id' => $item->id, 'type' => $type],
                [
                    'request_id'    => $req->id,
                    'user_id'       => $uploaderId,   // quién lo subió (admin o el cliente)
                    'file_path'     => $folder . $file,
                    'original_name' => $file,
                ]
            );
        }
    }

    private function data(): array
    {
        return [
            'Petro Perú' => [
                'email' => 'user@gmail.com', 'dni' => '70000000', // ya existe (lo crea DatabaseSeeder)
                'requests' => [
                    [   // Wendy: antecedentes con estados independientes; dirección escrita como TEXTO
                        'dni' => '72830595', 'names' => 'Wendy Esteferi', 'surnames' => 'Quice Cedeñeda',
                        'email' => 'wendy@correo.com', 'phone' => '987654321', 'date' => '2026-09-26',
                        'observations' => 'Validar estudios universitarios',
                        'services' => [
                            'antecedentes_policiales' => 'realizado', 'antecedentes_judiciales' => 'realizado',
                            'antecedentes_penales' => 'en_progreso',
                            'crediticias' => 'realizado', 'laborales' => 'cancelado', 'record' => 'realizado',
                            'academicas' => 'realizado', 'direccion' => 'realizado',
                        ],
                        'texts' => ['direccion' => 'Av. Bertolotto 752, San Miguel'],
                        'results' => [
                            'antecedentes_policiales' => '72830595_policiales.pdf', 'antecedentes_judiciales' => '72830595_judiciales.pdf',
                            'crediticias' => '72830595_crediticias.pdf', 'record' => '72830595_record_laboral.pdf',
                            'academicas' => '72830595_academicas.pdf',
                        ],
                        'attachments' => ['academicas' => '72830595_certificado_estudios.pdf'],
                    ],
                    [   // Heiter: todo cancelado o en proceso
                        'dni' => '75953952', 'names' => 'Heiter David', 'surnames' => 'Alvarez Paredes',
                        'email' => 'heiter@correo.com', 'phone' => '976543210', 'date' => '2026-09-24',
                        'services' => [
                            'antecedentes_policiales' => 'cancelado', 'antecedentes_judiciales' => 'cancelado',
                            'antecedentes_penales' => 'cancelado',
                            'crediticias' => 'realizado', 'laborales' => 'cancelado',
                            'record' => 'en_progreso', 'academicas' => 'en_progreso',
                        ],
                        'results' => ['crediticias' => '75953952_crediticias.pdf'],
                    ],
                    [   // Elizabeth: dirección en TEXTO y referencia en PDF
                        'dni' => '71055901', 'names' => 'Elizabeth Yajaira', 'surnames' => 'Livia Flores',
                        'email' => 'elizabeth@correo.com', 'phone' => '965432109', 'date' => '2026-09-20',
                        'services' => [
                            'antecedentes_policiales' => 'realizado', 'antecedentes_judiciales' => 'realizado',
                            'antecedentes_penales' => 'realizado', 'crediticias' => 'realizado', 'laborales' => 'en_progreso',
                            'record' => 'realizado', 'academicas' => 'realizado', 'domiciliarias' => 'realizado',
                            'reniec' => 'realizado', 'direccion' => 'realizado', 'referencia' => 'realizado',
                        ],
                        'texts' => ['direccion' => 'Jr. Turín 103, San Martín de Porres'],
                        'results' => [
                            'antecedentes_policiales' => '71055901_policiales.pdf', 'antecedentes_judiciales' => '71055901_judiciales.pdf',
                            'antecedentes_penales' => '71055901_penales.pdf', 'crediticias' => '71055901_crediticias.pdf',
                            'record' => '71055901_record_laboral.pdf', 'academicas' => '71055901_academicas.pdf',
                            'domiciliarias' => '71055901_domiciliarias.pdf', 'reniec' => '71055901_reniec.pdf',
                        ],
                        'attachments' => [
                            'laborales' => '71055901_certificado_trabajo.pdf',
                            'referencia' => '71055901_croquis_referencia.pdf',   // referencia enviada como PDF
                        ],
                    ],
                ],
            ],

            // Una solicitud por cada una de las otras empresas (candidatos distintos para diferenciarlas)
            'Claro Perú' => [
                'email' => 'claro@gmail.com', 'dni' => '70000001',
                'requests' => [[
                    'dni' => '45123456', 'names' => 'Carlos', 'surnames' => 'Mendoza Rojas',
                    'email' => 'carlos@correo.com', 'phone' => '987000001', 'date' => '2026-09-27',
                    'services' => ['antecedentes_policiales' => 'realizado', 'antecedentes_judiciales' => 'en_progreso', 'crediticias' => 'en_progreso'],
                    'results' => ['antecedentes_policiales' => '45123456_policiales.pdf'],
                ]],
            ],
            'Backus' => [
                'email' => 'backus@gmail.com', 'dni' => '70000002',
                'requests' => [[
                    'dni' => '46234567', 'names' => 'Lucía', 'surnames' => 'Torres Vega',
                    'email' => 'lucia@correo.com', 'phone' => '987000002', 'date' => '2026-09-25',
                    'services' => ['academicas' => 'en_espera', 'laborales' => 'en_progreso'],
                    'attachments' => ['laborales' => '46234567_certificado_trabajo.pdf'],
                ]],
            ],
            'Interbank' => [
                'email' => 'interbank@gmail.com', 'dni' => '70000003',
                'requests' => [[
                    'dni' => '47345678', 'names' => 'Jorge', 'surnames' => 'Salazar Díaz',
                    'email' => 'jorge@correo.com', 'phone' => '987000003', 'date' => '2026-09-24',
                    'services' => ['domiciliarias' => 'cancelado', 'direccion' => 'en_espera'],
                    'texts' => ['direccion' => 'Av. Arequipa 1200, Lince'],
                ]],
            ],
            'Alicorp' => [
                'email' => 'alicorp@gmail.com', 'dni' => '70000004',
                'requests' => [[
                    'dni' => '48456789', 'names' => 'María', 'surnames' => 'Quispe Huamán',
                    'email' => 'maria@correo.com', 'phone' => '987000004', 'date' => '2026-09-23',
                    'observations' => 'Urgente: inicia el lunes',
                    'services' => ['antecedentes_policiales' => 'realizado', 'antecedentes_penales' => 'en_espera', 'record' => 'en_espera', 'reniec' => 'realizado'],
                    'results' => ['antecedentes_policiales' => '48456789_policiales.pdf', 'reniec' => '48456789_reniec.pdf'],
                ]],
            ],
        ];
    }
}
