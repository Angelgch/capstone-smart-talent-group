<?php

// app/Http/Controllers/User/RequestController.php
// Lo que el USUARIO hace con sus solicitudes: ver su resumen (matriz), crear, y ver el detalle.

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\RequestService;
use App\Models\VerificationRequest;
use App\Support\ServiceCatalog;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class RequestController extends Controller
{
    // MATRIZ del usuario: tabla resumen SOLO con sus solicitudes
    public function index(Request $request)
    {
        $status = in_array($request->status, ['en_espera', 'en_progreso', 'realizado', 'cancelado'])
            ? $request->status : null;

        $solicitudes = VerificationRequest::where('user_id', $request->user()->id)
            // cuántos servicios ya tienen informe (para activar el botón de descarga)
            ->withCount(['services as results_count' => fn ($q) =>
                $q->whereHas('documents', fn ($d) => $d->where('type', 'informe_admin'))])
            ->when($request->filled('q'), function ($query) use ($request) {
                $like = '%' . $request->q . '%';
                $query->where(fn ($w) => $w->where('dni', 'like', $like)
                    ->orWhere('names', 'like', $like)
                    ->orWhere('surnames', 'like', $like));
            })
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->get();

        return view('user.requests.matrix', ['solicitudes' => $solicitudes]);
    }

    // Formulario "Nueva solicitud"
    public function create()
    {
        return view('user.requests.create', [
            'services' => ServiceCatalog::all(),
            'extras'   => ServiceCatalog::extras(),
        ]);
    }

    // GUARDA la solicitud: cabecera + servicios + documentos (todo o nada)
    public function store(Request $request)
    {
        $data = $request->validate([
            'dni'                => ['required', 'digits:8'],
            'names'              => ['required', 'string', 'max:255'],
            'surnames'           => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255'],
            'phone'              => ['required', 'digits_between:7,9'],
            'observations'       => ['nullable', 'string', 'max:2000'],
            'services'           => ['required', 'array', 'min:1'],
            'services.*'         => ['string', Rule::in(ServiceCatalog::keys())],   // no se aceptan servicios inventados
            'texts.direccion'    => ['nullable', 'string', 'max:500'],
            'texts.referencia'   => ['nullable', 'string', 'max:500'],
            'documents.*'        => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'], // 5 MB
        ], [
            'dni.digits'              => 'El DNI debe tener exactamente 8 dígitos.',
            'phone.digits_between'    => 'El teléfono debe tener entre 7 y 9 dígitos.',
            'services.required'       => 'Seleccione al menos un servicio.',
            'services.min'            => 'Seleccione al menos un servicio.',
            'documents.*.mimes'       => 'Los documentos deben ser PDF, JPG o PNG.',
            'documents.*.max'         => 'Cada documento puede pesar máximo 5 MB.',
        ]);

        // Si eligió "Sí" enviar documento, el archivo es obligatorio
        foreach ($data['services'] as $key) {
            if ($request->input("doc_choice.$key") === 'si' && ! $request->hasFile("documents.$key")) {
                throw ValidationException::withMessages([
                    "documents.$key" => 'Adjunta el documento de "' . ServiceCatalog::all()[$key]['full'] . '" o elige "No".',
                ]);
            }
        }

        $stored = []; // rutas guardadas: si algo falla se borran

        try {
            $solicitud = DB::transaction(function () use ($request, $data, &$stored) {
                // CABECERA
                $solicitud = VerificationRequest::create([
                    'user_id'      => $request->user()->id,   // responsable
                    'dni'          => $data['dni'],
                    'names'        => $data['names'],
                    'surnames'     => $data['surnames'],
                    'email'        => Str::lower($data['email']),
                    'phone'        => $data['phone'],
                    'observations' => $data['observations'] ?? null,
                ]);

                // DETALLE: un servicio marcado = una fila (+ su documento si eligió "Sí")
                foreach ($data['services'] as $key) {
                    $item = $solicitud->services()->create(['service' => $key]);

                    if ($request->input("doc_choice.$key") === 'si' && $request->hasFile("documents.$key")) {
                        $this->saveDocument($item, $request->file("documents.$key"), $stored);
                    }
                }

                // Dirección y referencia: TEXTO o PDF (si no llenó nada, no se crea la fila)
                foreach (array_keys(ServiceCatalog::extras()) as $key) {
                    $file = $request->file("documents.$key");
                    $text = trim((string) data_get($data, "texts.$key"));
                    if (! $file && $text === '') continue;

                    $item = $solicitud->services()->create(['service' => $key, 'detail' => $file ? null : $text]);
                    if ($file) $this->saveDocument($item, $file, $stored);
                }

                $solicitud->refreshStatus();

                return $solicitud;
            });
        } catch (\Throwable $e) {
            foreach ($stored as $path) Storage::disk('local')->delete($path);
            throw $e;
        }

        return redirect()->route('user.requests.index')
            ->with('status', 'Solicitud ' . $solicitud->code . ' creada correctamente.');
    }

    // DETALLE de una solicitud (solo lectura por ahora)
    public function show(Request $request, VerificationRequest $verificationRequest)
    {
        // Solo el dueño. Si es de otro usuario => 404 (no se revela que existe)
        abort_unless($verificationRequest->user_id === $request->user()->id, 404);

        $verificationRequest->load(['user', 'services.attachment', 'services.result']);

        return view('user.requests.show', [
            'solicitud' => $verificationRequest,
            'services'  => ServiceCatalog::all(),
            'extras'    => ServiceCatalog::extras(),
        ]);
    }

    // Guarda el archivo en storage PRIVADO (no es accesible por URL) y registra el documento
    private function saveDocument(RequestService $item, UploadedFile $file, array &$stored): void
    {
        $path = $file->store("requests/{$item->request_id}/{$item->service}", 'local');
        $stored[] = $path;

        Document::create([
            'request_id'         => $item->request_id,
            'request_service_id' => $item->id,
            'user_id'            => auth()->id(),            // quién lo subió
            'type'               => 'requisito_cliente',     // lo sube el cliente al postular
            'file_path'          => $path,
            'original_name'      => $file->getClientOriginalName(),
        ]);
    }
}
