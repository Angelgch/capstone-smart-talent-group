<?php

// app/Http/Controllers/User/RequestController.php
// TODO lo que el USUARIO hace con sus solicitudes: matriz, crear, ver/editar, eliminar y exportar.
// Seguridad: TODA consulta se limita a sus propias solicitudes (user_id). Lo ajeno da 404.

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\RequestService;
use App\Models\VerificationRequest;
use App\Support\ServiceCatalog;
use App\Support\StatusLabel;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RequestController extends Controller
{
    /* ----------------------------------------------------------------
       MATRIZ (resumen) + EXCEL
       ---------------------------------------------------------------- */
    public function index(Request $request)
    {
        $solicitudes = $this->listQuery($request)->paginate(15)->withQueryString();

        return view('user.requests.matrix', compact('solicitudes'));
    }

    // Exporta EXACTAMENTE lo que se ve en la matriz (mismos filtros). CSV que Excel abre sin problemas.
    public function export(Request $request)
    {
        $rows = $this->listQuery($request)->get();

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
       CREAR
       ---------------------------------------------------------------- */
    public function create()
    {
        return view('user.requests.create', ['services' => ServiceCatalog::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules(true), $this->messages());

        // Si eligió "Sí" enviar documento, el archivo es obligatorio
        foreach ($data['services'] as $key) {
            if ($request->input("doc_choice.$key") === 'si' && ! $request->hasFile("documents.$key")) {
                throw ValidationException::withMessages([
                    "documents.$key" => 'Adjunta el documento de "' . ServiceCatalog::all()[$key]['full'] . '" o elige "No".',
                ]);
            }
        }

        $nuevos = []; // archivos guardados: si algo falla se borran

        try {
            $solicitud = DB::transaction(function () use ($request, $data, &$nuevos) {
                $solicitud = VerificationRequest::create([
                    'user_id'      => $request->user()->id,   // responsable (lo decide el servidor)
                    'dni'          => $data['dni'],
                    'names'        => $data['names'],
                    'surnames'     => $data['surnames'],
                    'email'        => Str::lower($data['email']),
                    'phone'        => $data['phone'],
                    'address'      => $data['address'] ?? null,
                    'reference'    => $data['reference'] ?? null,
                    'observations' => $data['observations'] ?? null,
                ]);

                // Un servicio marcado = una fila (+ su documento si eligió "Sí")
                $aBorrar = []; // al crear no se reemplaza nada, pero syncDocument lo pide
                foreach ($data['services'] as $key) {
                    $item = $solicitud->services()->create(['service' => $key]);
                    $this->syncDocument($request, $solicitud, $item, $key, $nuevos, $aBorrar);
                }

                $solicitud->refreshStatus();

                return $solicitud;
            });
        } catch (\Throwable $e) {
            foreach ($nuevos as $path) Storage::disk('local')->delete($path);
            throw $e;
        }

        return redirect()->route('user.requests.index')
            ->with('status', 'Solicitud ' . $solicitud->code . ' creada correctamente.');
    }

    /* ----------------------------------------------------------------
       DETALLE (formulario editable) y GUARDAR CAMBIOS
       ---------------------------------------------------------------- */
    public function show(int $solicitud)
    {
        $sol = $this->own($solicitud)->load('services.attachment');

        return view('user.requests.show', [
            'solicitud' => $sol,
            'services'  => ServiceCatalog::all(),
            'canEdit'   => $this->canEdit($sol),
            'extras'    => [],   // TEMPORAL: la vista actual todavía lo pide; se quita con la vista nueva
        ]);
    }

    public function update(Request $request, int $solicitud)
    {
        $sol = $this->own($solicitud);
        abort_unless($this->canEdit($sol), 403, 'Esta solicitud ya no puede editarse.');

        $data = $request->validate($this->rules(false), $this->messages());

        $nuevos  = [];  // archivos guardados ahora (si algo falla, se borran)
        $aBorrar = [];  // archivos reemplazados o eliminados (se borran SOLO después de guardar en la BD)

        try {
            DB::transaction(function () use ($request, $data, $sol, &$nuevos, &$aBorrar) {
                // Datos del candidato (el DNI no se puede cambiar)
                $sol->update([
                    'names'        => $data['names'],
                    'surnames'     => $data['surnames'],
                    'email'        => Str::lower($data['email']),
                    'phone'        => $data['phone'],
                    'address'      => $data['address'] ?? null,
                    'reference'    => $data['reference'] ?? null,
                    'observations' => $data['observations'] ?? null,
                ]);

                $items = $sol->services()->with('attachment')->get()->keyBy('service');

                foreach (ServiceCatalog::keys() as $key) {
                    $item    = $items->get($key);
                    $marcado = in_array($key, $data['services'], true);

                    if (! $item) {
                        if (! $marcado) continue;                               // no pedido y sigue sin pedirse
                        $item = $sol->services()->create(['service' => $key]);  // servicio NUEVO
                    } elseif ($item->status === 'en_progreso') {
                        // Bloqueado: ya está en trámite, no se puede quitar (se ignora si lo desmarcó)
                    } elseif ($item->status === 'en_espera' && ! $marcado) {
                        // Todavía no empezó: se elimina el servicio y sus documentos
                        foreach ($item->documents as $d) $aBorrar[] = $d->file_path;
                        $item->delete();
                        continue;
                    } elseif ($item->status === 'realizado' && ! $marcado) {
                        $item->update(['status' => 'cancelado']);   // se cancela (la pantalla avisó de la penalización)
                        continue;
                    } elseif ($item->status === 'cancelado') {
                        if (! $marcado) continue;
                        $item->update(['status' => 'en_espera']);   // lo volvió a marcar: se reactiva
                    }

                    $this->syncDocument($request, $sol, $item, $key, $nuevos, $aBorrar);
                }

                $sol->refreshStatus();
            });
        } catch (\Throwable $e) {
            foreach ($nuevos as $path) Storage::disk('local')->delete($path);
            throw $e;
        }

        foreach ($aBorrar as $path) Storage::disk('local')->delete($path);

        return redirect()->route('user.requests.show', $sol)->with('status', 'Cambios guardados correctamente.');
    }

    /* ----------------------------------------------------------------
       ELIMINAR (pide la contraseña del usuario)
       ---------------------------------------------------------------- */
    public function destroy(Request $request, int $solicitud)
    {
        $sol = $this->own($solicitud);

        // "current_password" compara con la contraseña del usuario que tiene la sesión iniciada
        $request->validate(['password' => ['required', 'current_password']], [
            'password.required'         => 'Ingresa tu contraseña para eliminar la solicitud.',
            'password.current_password' => 'La contraseña es incorrecta. La solicitud no se eliminó.',
        ]);

        $paths = $sol->documents()->pluck('file_path');
        $code  = $sol->code;

        DB::transaction(fn () => $sol->delete()); // servicios y documentos se borran por cascada

        foreach ($paths as $path) Storage::disk('local')->delete($path);

        return redirect()->route('user.requests.index')->with('status', 'Solicitud ' . $code . ' eliminada.');
    }

    /* ----------------------------------------------------------------
       Auxiliares privados
       ---------------------------------------------------------------- */

    // Solo SUS solicitudes: si es de otro usuario, 404 (no se revela que existe)
    private function own(int $id): VerificationRequest
    {
        return VerificationRequest::where('user_id', auth()->id())->findOrFail($id);
    }

    // Solo se edita mientras está en espera o en proceso
    private function canEdit(VerificationRequest $sol): bool
    {
        return in_array($sol->status, ['en_espera', 'en_progreso'], true);
    }

    // Consulta de la matriz (la usan la tabla y el Excel, así siempre coinciden)
    private function listQuery(Request $request)
    {
        return VerificationRequest::where('user_id', $request->user()->id)
            ->with('user')
            // cuántos servicios ya tienen informe (activa el botón naranja de la matriz)
            ->withCount(['services as results_count' => fn ($q) =>
                $q->whereHas('documents', fn ($d) => $d->where('type', 'informe_admin'))])
            ->search($request->input('q'), $request->input('status'))
            ->latest();
    }

    // Evita que Excel ejecute fórmulas si un texto empieza con = + - @
    private function safe(?string $value): string
    {
        $value = (string) $value;
        return preg_match('/^[=+\-@]/', $value) ? "'" . $value : $value;
    }

    // Documento de UN servicio según la opción elegida (doc_choice): no = quitar | si + archivo = reemplazar | si = conservar
    private function syncDocument(Request $request, VerificationRequest $sol, RequestService $item, string $key, array &$nuevos, array &$aBorrar): void
    {
        $choice = $request->input("doc_choice.$key");   // 'si' | 'no' | null
        $file   = $request->file("documents.$key");
        $actual = $item->attachment;                    // documento actual (o null)

        if ($choice === 'no') {
            if ($actual) {
                $aBorrar[] = $actual->file_path;
                $actual->delete();
            }
            return;
        }

        if ($choice !== 'si') return;                   // no llegó la opción: no se toca nada

        if ($file) {
            $path = $file->store("requests/{$sol->id}/{$key}", 'local');
            $nuevos[] = $path;

            $fields = [
                'user_id'       => auth()->id(),
                'file_path'     => $path,
                'original_name' => $file->getClientOriginalName(),
            ];

            if ($actual) {
                $aBorrar[] = $actual->file_path;        // el archivo viejo se borra después de guardar
                $actual->update($fields);
            } else {
                Document::create($fields + [
                    'request_id'         => $sol->id,
                    'request_service_id' => $item->id,
                    'type'               => 'requisito_cliente',
                ]);
            }
            return;
        }

        if (! $actual) {
            throw ValidationException::withMessages([
                "documents.$key" => 'Adjunta el documento de "' . ServiceCatalog::all()[$key]['full'] . '" o elige "No".',
            ]);
        }
        // "Sí" sin archivo nuevo y con documento actual: se conserva
    }

    private function rules(bool $creating): array
    {
        return array_merge(
            $creating ? ['dni' => ['required', 'digits:8']] : [],   // el DNI solo se escribe al crear
            [
                'names'        => ['required', 'string', 'max:255'],
                'surnames'     => ['required', 'string', 'max:255'],
                'email'        => ['required', 'email', 'max:255'],
                'phone'        => ['required', 'digits_between:7,9'],
                'address'      => ['nullable', 'string', 'max:500'],
                'reference'    => ['nullable', 'string', 'max:500'],
                'observations' => ['nullable', 'string', 'max:2000'],
                'services'     => ['required', 'array', 'min:1'],
                'services.*'   => ['string', Rule::in(ServiceCatalog::keys())],   // no se aceptan servicios inventados
                'documents.*'  => ['nullable', 'file', 'extensions:pdf,jpg,jpeg,png', 'max:10240'], // 10 MB (o 20280 si prefieres 20MB)
            ]
        );
    }

    private function messages(): array
    {
        return [
            'dni.digits'           => 'El DNI debe tener exactamente 8 dígitos.',
            'phone.digits_between' => 'El teléfono debe tener entre 7 y 9 dígitos.',
            'services.required'    => 'Seleccione al menos un servicio.',
            'services.min'         => 'Seleccione al menos un servicio.',
            //'documents.*.mimes'    => 'Los documentos deben ser PDF, JPG o PNG.',
            //'documents.*.max'      => 'Cada documento puede pesar máximo 5 MB.',
            'documents.*.extensions' => 'Los documentos deben ser PDF, JPG o PNG.',
            'documents.*.max' => 'Cada documento puede pesar máximo 10 MB.',
            'documents.*.uploaded' => 'No se pudo subir un archivo: supera el tamaño que permite el servidor.',
            
        ];
    }
}