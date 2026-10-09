<?php

// app/Http/Controllers/User/DocumentController.php
// Documentos del USUARIO: la página "Descargas" (informes que le envió el admin) y la entrega de archivos.
// Seguridad: solo documentos de SUS solicitudes. Lo ajeno da 404.

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\VerificationRequest;
use App\Support\DocumentFiles;
use App\Support\ServiceCatalog;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    // Página Descargas: un informe por servicio (los que subió el admin)
    public function index(int $solicitud)
    {
        $sol = $this->own($solicitud)->load('services.result');

        return view('user.requests.downloads', [
            'solicitud' => $sol,
            'services'  => ServiceCatalog::all(),
        ]);
    }

    // Un archivo (?view=1 lo abre en el navegador)
    public function download(Request $request, int $document)
    {
        $doc = Document::whereHas('verificationRequest', fn ($q) => $q->where('user_id', auth()->id()))
            ->findOrFail($document);

        return DocumentFiles::response($doc, $request->boolean('view'));
    }

    // Todos en .zip.  type: informe_admin (informes) | requisito_cliente (lo que él envió)
    public function zip(int $solicitud, string $type)
    {
        abort_unless(in_array($type, ['informe_admin', 'requisito_cliente'], true), 404);
        $sol = $this->own($solicitud);

        $docs  = Document::where('request_id', $sol->id)->where('type', $type)->with('requestService')->get();
        $label = $type === 'informe_admin' ? 'informes' : 'archivos';

        return DocumentFiles::zip($docs, $sol->code . '_' . $label . '.zip');
    }

    private function own(int $id): VerificationRequest
    {
        return VerificationRequest::where('user_id', auth()->id())->findOrFail($id);
    }
}