<?php

// app/Http/Controllers/Admin/CompanyController.php
// Lo que el admin ve por empresa: lista de empresas, MATRIZ (resumen) y DETALLE de una solicitud.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\VerificationRequest;
use App\Support\ServiceCatalog;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Gestión de Empresas
    public function index()
    {
        return view('admin.companies.index', [
            'companies' => Company::orderBy('name')->get(),
        ]);
    }

    // MATRIZ: tabla resumen con las solicitudes de ESA empresa (usuarios con su mismo RUC)
    public function matrix(Request $request, Company $company)
    {
        $status = in_array($request->status, ['en_espera', 'en_progreso', 'realizado', 'cancelado'])
            ? $request->status : null;

        $solicitudes = $company->verificationRequests()
            ->with('user')   // el responsable
            // Buscador (se usa verification_requests.xxx porque "users" también tiene dni)
            ->when($request->filled('q'), function ($query) use ($request) {
                $like = '%' . $request->q . '%';
                $query->where(fn ($w) => $w
                    ->where('verification_requests.dni', 'like', $like)
                    ->orWhere('verification_requests.names', 'like', $like)
                    ->orWhere('verification_requests.surnames', 'like', $like));
            })
            ->when($status, fn ($query) => $query->where('verification_requests.status', $status))
            ->latest('verification_requests.created_at')
            ->get();

        return view('admin.companies.matrix', [
            'company'     => $company,
            'solicitudes' => $solicitudes,
        ]);
    }

    // DETALLE: ficha completa de una solicitud (servicios, estados, dirección, referencia, documentos)
    public function show(Company $company, VerificationRequest $verificationRequest)
    {
        $verificationRequest->load(['user', 'services.attachment', 'services.result']);

        // La solicitud debe ser de esa empresa; si no, 404
        abort_unless($verificationRequest->user && $verificationRequest->user->ruc === $company->ruc, 404);

        return view('admin.companies.show', [
            'company'   => $company,
            'solicitud' => $verificationRequest,
            'services'  => ServiceCatalog::all(),
            'extras'    => ServiceCatalog::extras(),
        ]);
    }
}