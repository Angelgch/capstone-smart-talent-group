<?php

// app/Http/Controllers/Admin/CompanyController.php
// Lo que el admin hace con las empresas: listar, REGISTRAR, ver su matriz y el detalle de una solicitud.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\VerificationRequest;
use App\Support\ServiceCatalog;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    // Gestión de Empresas: lista
    public function index()
    {
        return view('admin.companies.index', [
            'companies' => Company::orderBy('trade_name')->get(),
        ]);
    }

    // Formulario "Nueva empresa"
    public function create()
    {
        return view('admin.companies.create');
    }

    // Guarda la empresa. El RUC no puede repetirse.
    public function store(Request $request)
    {
        $data = $request->validate([
            'ruc'        => ['required', 'digits:11', 'unique:companies,ruc'],
            'legal_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['required', 'string', 'max:255'],
            'address'    => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'digits_between:7,15'],
        ], [
            'ruc.required'        => 'El RUC es obligatorio.',
            'ruc.digits'          => 'El RUC debe tener exactamente 11 dígitos.',
            'ruc.unique'          => 'Ya existe una empresa registrada con ese RUC.',
            'legal_name.required' => 'La razón social es obligatoria.',
            'trade_name.required' => 'El nombre comercial es obligatorio.',
            'phone.digits_between' => 'El teléfono debe tener entre 7 y 15 dígitos.',
        ]);

        $company = Company::create($data);

        return redirect()->route('admin.companies.index')
            ->with('status', 'Empresa "' . $company->name . '" registrada correctamente.');
    }

    // MATRIZ: tabla resumen con las solicitudes de ESA empresa (las de sus usuarios)
    public function matrix(Request $request, Company $company)
    {
        $status = in_array($request->status, ['en_espera', 'en_progreso', 'realizado', 'cancelado'])
            ? $request->status : null;

        $solicitudes = $company->verificationRequests()
            ->with('user')   // el responsable
            // cuántos servicios tienen archivo del usuario (para activar el botón naranja de descarga)
            ->withCount(['services as attachments_count' => fn ($q) =>
                $q->whereHas('documents', fn ($d) => $d->where('type', 'requisito_cliente'))])
            // Buscador (se usa requests.xxx porque "users" también tiene dni)
            ->when($request->filled('q'), function ($query) use ($request) {
                $like = '%' . $request->q . '%';
                $query->where(fn ($w) => $w
                    ->where('requests.dni', 'like', $like)
                    ->orWhere('requests.names', 'like', $like)
                    ->orWhere('requests.surnames', 'like', $like));
            })
            ->when($status, fn ($query) => $query->where('requests.status', $status))
            ->latest('requests.created_at')
            ->get();

        return view('admin.companies.matrix', [
            'company'     => $company,
            'solicitudes' => $solicitudes,
        ]);
    }

    // DETALLE: ficha completa de una solicitud
    public function show(Company $company, VerificationRequest $verificationRequest)
    {
        $verificationRequest->load(['user', 'services.attachment', 'services.result']);

        // La solicitud debe ser de esa empresa; si no, 404
        abort_unless($verificationRequest->user && $verificationRequest->user->company_id === $company->id, 404);

        return view('admin.companies.show', [
            'company'   => $company,
            'solicitud' => $verificationRequest,
            'services'  => ServiceCatalog::all(),
            'extras'    => ServiceCatalog::extras(),
        ]);
    }
}
