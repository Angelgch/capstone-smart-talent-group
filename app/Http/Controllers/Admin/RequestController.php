<?php

// app/Http/Controllers/Admin/RequestController.php
// TODO lo que el ADMIN hace con las solicitudes de una empresa: matriz, detalle (editar estados e informes) y Excel.
// El admin NO crea ni elimina solicitudes. Seguridad: la solicitud se busca SIEMPRE dentro de la empresa
// (si no es de esa empresa => 404).

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Document;
use App\Support\ServiceCatalog;
use App\Support\StatusLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RequestController extends Controller
{
    /* ----------------------------------------------------------------
       MATRIZ (resumen) + EXCEL
       ---------------------------------------------------------------- */
    public function matrix(Request $request, Company $company)
    {
        $solicitudes = $this->listQuery($request, $company)->paginate(15)->withQueryString();

        return view('admin.companies.matrix', compact('company', 'solicitudes'));
    }

    // Exporta EXACTAMENTE lo que se ve en la matriz (mismos filtros). CSV que Excel abre sin problemas.
    public function export(Request $request, Company $company)
    {
        $rows = $this->listQuery($request, $company)->get();

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM: Excel lee bien las tildes
            fputcsv($out, ['N° Solicitud', 'Fecha', 'Responsable', 'DNI', 'Nombres', 'Apellidos', 'Estado'], ';');

            foreach ($rows as $s) {
                fputcsv($out, [
                    $s->code,
                    $s->created_at->format('d/m/Y'),
                    $this->safe($s->user->name),
                    $this->safe($s->dni),
                    $this->safe($s->names),
                    $this->safe($s->surnames),
                    StatusLabel::general($s->status),
                ], ';');
            }
            fclose($out);
        }, 'solicitudes_' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /* ----------------------------------------------------------------
       DETALLE (editable) y GUARDAR
       ---------------------------------------------------------------- */
    public function show(Company $company, int $solicitud)
    {
        // Se cargan los informes del admin. Los archivos del usuario NO se cargan: esos se ven en Descargas.
        $sol = $this->find($company, $solicitud)->load('user', 'services.result');

        return view('admin.companies.show', [
            'company'   => $company,
            'solicitud' => $sol,
            'services'  => ServiceCatalog::all(),
            'extras'    => [],   // TEMPORAL: la vista actual todavía lo pide; se quita con la vista nueva
        ]);
    }

    public function update(Request $request, Company $company, int $solicitud)
    {
        $sol = $this->find($company, $solicitud);

        $data = $request->validate([
            'status'         => ['array'],
            'status.*'       => ['nullable', Rule::in(['en_espera', 'en_progreso', 'realizado', 'cancelado'])],
            'informe.*'      => ['nullable', 'file', 'mimes:pdf', 'max:10240'],   // solo PDF, 10 MB
            'general_status' => ['nullable', Rule::in(['en_espera', 'en_progreso', 'realizado', 'cancelado'])],
        ], [
            'informe.*.mimes' => 'Los informes deben ser PDF.',
            'informe.*.max'   => 'Cada informe puede pesar máximo 10 MB.',
        ]);

        $nuevos  = [];  // informes guardados ahora (si algo falla, se borran)
        $aBorrar = [];  // informes reemplazados (se borran SOLO después de guardar en la BD)

        try {
            DB::transaction(function () use ($request, $data, $sol, &$nuevos, &$aBorrar) {
                // Solo se recorren los servicios que el usuario pidió (lo demás que llegue se ignora)
                foreach ($sol->services()->with('result')->get() as $item) {
                    $key    = $item->service;
                    $nuevo  = data_get($data, "status.$key", $item->status);
                    $file   = $request->file("informe.$key");

                    // "Realizado" exige informe (el que ya estaba o el que se sube ahora)
                    if ($nuevo === 'realizado' && ! $item->result && ! $file) {
                        throw ValidationException::withMessages([
                            "status.$key" => 'Para marcar "Realizado" en ' . ServiceCatalog::all()[$key]['full'] . ' sube primero el informe PDF.',
                        ]);
                    }

                    $item->update(['status' => $nuevo]);

                    if ($file) {
                        $path = $file->store("requests/{$sol->id}/{$key}", 'local');
                        $nuevos[] = $path;

                        $fields = [
                            'user_id'       => auth()->id(),   // el admin que lo subió
                            'file_path'     => $path,
                            'original_name' => $file->getClientOriginalName(),
                        ];

                        if ($item->result) {
                            $aBorrar[] = $item->result->file_path;
                            $item->result->update($fields);
                        } else {
                            Document::create($fields + [
                                'request_id'         => $sol->id,
                                'request_service_id' => $item->id,
                                'type'               => 'informe_admin',
                            ]);
                        }
                    }
                }

                // Estado general: si el admin cambió el selector se respeta; si no, se calcula solo
                $manual = $data['general_status'] ?? null;
                if ($manual && $manual !== $sol->status) {
                    $sol->update(['status' => $manual]);
                } else {
                    $sol->refreshStatus();
                }
            });
        } catch (\Throwable $e) {
            foreach ($nuevos as $path) Storage::disk('local')->delete($path);
            throw $e;
        }

        foreach ($aBorrar as $path) Storage::disk('local')->delete($path);

        return redirect()->route('admin.companies.requests.show', [$company, $sol])
            ->with('status', 'Cambios guardados correctamente.');
    }

    /* ----------------------------------------------------------------
       Auxiliares privados
       ---------------------------------------------------------------- */

    // La solicitud se busca DENTRO de la empresa: si es de otra, 404
    private function find(Company $company, int $id)
    {
        return $company->verificationRequests()->where('requests.id', $id)->firstOrFail();
    }

    // Consulta de la matriz (la usan la tabla y el Excel, así siempre coinciden)
    private function listQuery(Request $request, Company $company)
    {
        return $company->verificationRequests()
            ->with('user')
            // cuántos servicios tienen archivo del usuario (activa el botón naranja de la matriz)
            ->withCount(['services as attachments_count' => fn ($q) =>
                $q->whereHas('documents', fn ($d) => $d->where('type', 'requisito_cliente'))])
            ->search($request->input('q'), $request->input('status'))
            ->latest('requests.created_at');   // con prefijo: users también tiene created_at
    }

    // Evita que Excel ejecute fórmulas si un texto empieza con = + - @
    private function safe(?string $value): string
    {
        $value = (string) $value;
        return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
    }
}