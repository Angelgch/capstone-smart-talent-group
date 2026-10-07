<?php

// app/Http/Controllers/Admin/DocumentController.php
// Documentos del ADMIN: la página "Descargas" (archivos que envió el usuario) y la entrega de archivos.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Document;
use App\Support\DocumentFiles;
use App\Support\ServiceCatalog;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    // Página Descargas: lo que el usuario envió, por servicio
    public function index(Company $company, int $solicitud)
    {
        $sol = $this->find($company, $solicitud)->load('user', 'services.attachment');

        return view('admin.companies.downloads', [
            'company'   => $company,
            'solicitud' => $sol,
            'services'  => ServiceCatalog::all(),
        ]);
    }

    // Un archivo (?view=1 lo abre en el navegador). El admin puede abrir cualquiera.
    public function download(Request $request, int $document)
    {
        return DocumentFiles::response(Document::findOrFail($document), $request->boolean('view'));
    }

    // Todos en .zip.  type: requisito_cliente (lo que envió el usuario) | informe_admin (informes)
    public function zip(Company $company, int $solicitud, string $type)
    {
        abort_unless(in_array($type, ['requisito_cliente', 'informe_admin'], true), 404);
        $sol = $this->find($company, $solicitud);

        $docs  = Document::where('request_id', $sol->id)->where('type', $type)->with('requestService')->get();
        $label = $type === 'requisito_cliente' ? 'archivos' : 'informes';

        return DocumentFiles::zip($docs, $sol->code . '_' . $label . '.zip');
    }

    // La solicitud se busca DENTRO de la empresa: si es de otra, 404
    private function find(Company $company, int $id)
    {
        return $company->verificationRequests()->where('requests.id', $id)->firstOrFail();
    }
}