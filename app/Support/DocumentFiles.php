<?php

// app/Support/DocumentFiles.php   (REEMPLAZA al anterior)
// Utilidad técnica para ENTREGAR archivos (uno solo o varios en .zip) desde el storage privado.
// OJO: aquí NO hay permisos. Quién puede pedir qué lo decide cada controlador (User\ y Admin\).

namespace App\Support;

use App\Models\Document;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DocumentFiles
{
    // Un archivo: $inline = true lo abre en el navegador (visualizar); false lo descarga
    public static function response(Document $doc, bool $inline = false)
    {
        $disk = Storage::disk('local');
        abort_unless($disk->exists($doc->file_path), 404, 'El archivo no está disponible.');

        if (! $inline) {
            return $disk->download($doc->file_path, $doc->original_name);
        }

        // Al visualizar, el tipo se decide por la EXTENSIÓN permitida (no por el contenido del archivo)
        // y "nosniff" evita que el navegador lo reinterprete. Así un archivo disfrazado no se ejecuta.
        $mime = match (strtolower(pathinfo($doc->original_name, PATHINFO_EXTENSION))) {
            'pdf'         => 'application/pdf',
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            default       => null,
        };

        if (! $mime) {
            return $disk->download($doc->file_path, $doc->original_name); // tipo desconocido: solo descarga
        }

        return $disk->response($doc->file_path, $doc->original_name, [
            'Content-Type'           => $mime,
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    // Varios archivos en un .zip (solo los que existen en disco)
    public static function zip(Collection $docs, string $filename)
    {
        $disk = Storage::disk('local');
        $docs = $docs->filter(fn ($d) => $disk->exists($d->file_path));
        abort_if($docs->isEmpty(), 404, 'No hay archivos disponibles para descargar.');

        $zipPath = tempnam(sys_get_temp_dir(), 'stg');
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($docs as $d) {
            // Nombre dentro del zip: clave del servicio + nombre original (evita repetidos)
            $zip->addFile($disk->path($d->file_path), $d->requestService->service . '_' . $d->original_name);
        }
        $zip->close();

        return response()->download($zipPath, $filename)->deleteFileAfterSend(true);
    }
}