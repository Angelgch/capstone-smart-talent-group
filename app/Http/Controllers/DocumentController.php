<?php

// app/Http/Controllers/DocumentController.php   (REEMPLAZA el anterior)
// Descarga de archivos (uno solo o todos en .zip). Los archivos están en storage PRIVADO,
// por eso TODA descarga pasa por aquí y se comprueba quién la pide.
//   - admin: puede descargar de cualquier solicitud
//   - user : solo de sus propias solicitudes

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentController extends Controller
{
    // Un archivo. Con ?view=1 se abre en el navegador (visualizar); sin eso, se descarga.
    public function download(Request $request, Document $document)
    {
        $this->authorizeRequest($document->verificationRequest);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($document->file_path), 404, 'El archivo no está disponible.');

        return $request->boolean('view')
            ? $disk->response($document->file_path, $document->original_name)
            : $disk->download($document->file_path, $document->original_name);
    }

    // Todos los archivos de un tipo en un .zip.
    //   type: requisito_cliente (los del usuario) | informe_admin (los informes)
    public function zip(VerificationRequest $verificationRequest, string $type)
    {
        abort_unless(in_array($type, ['requisito_cliente', 'informe_admin']), 404);
        $this->authorizeRequest($verificationRequest);

        $disk = Storage::disk('local');

        $docs = Document::where('request_id', $verificationRequest->id)
            ->where('type', $type)
            ->with('requestService')
            ->get()
            ->filter(fn ($d) => $disk->exists($d->file_path)); // solo los que existen en disco

        abort_if($docs->isEmpty(), 404, 'No hay archivos disponibles para descargar.');

        $zipPath = tempnam(sys_get_temp_dir(), 'stg');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($docs as $d) {
            // Nombre dentro del zip: clave del servicio + nombre original (evita repetidos)
            $prefix = $d->requestService ? $d->requestService->service . '_' : '';
            $zip->addFile($disk->path($d->file_path), $prefix . $d->original_name);
        }
        $zip->close();

        $label = $type === 'informe_admin' ? 'informes' : 'archivos';

        return response()
            ->download($zipPath, $verificationRequest->code . '_' . $label . '.zip')
            ->deleteFileAfterSend(true);
    }

    // Admin: todo | User: solo lo suyo (si no, 404)
    private function authorizeRequest(VerificationRequest $solicitud): void
    {
        $user = auth()->user();
        abort_unless($user->isAdmin() || $solicitud->user_id === $user->id, 404);
    }
}
