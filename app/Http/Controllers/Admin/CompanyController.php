<?php

// app/Http/Controllers/Admin/CompanyController.php   (ARCHIVO COMPLETO: reemplaza el que tienes)
// Lo que el admin hace con las EMPRESAS: listar, registrar, editar (tuerquita) y eliminar.
// La matriz y el detalle de solicitudes están en Admin\RequestController.

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Document;
use App\Models\VerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $company = Company::create($request->validate($this->companyRules(), $this->companyMessages()));

        return redirect()->route('admin.companies.index')
            ->with('status', 'Empresa "' . $company->name . '" registrada correctamente.');
    }

    // Pantalla de la tuerquita: editar los datos de la empresa y, abajo, eliminarla
    public function edit(Company $company)
    {
        return view('admin.companies.edit', [
            'company'       => $company,
            'usersCount'    => $company->users()->count(),
            'requestsCount' => $company->verificationRequests()->count(),
        ]);
    }

    // Guarda los cambios (el RUC no puede repetirse con OTRA empresa)
    public function update(Request $request, Company $company)
    {
        $company->update($request->validate($this->companyRules($company->id), $this->companyMessages()));

        return redirect()->route('admin.companies.index')
            ->with('status', 'Empresa "' . $company->name . '" actualizada correctamente.');
    }

    // Elimina la empresa PIDIENDO LA CONTRASEÑA DEL ADMIN.
    // También se borran sus usuarios, las solicitudes de esos usuarios y todos sus archivos.
    public function destroy(Request $request, Company $company)
    {
        $request->validate(['password' => ['required', 'current_password']], [
            'password.required'         => 'Ingresa tu contraseña para eliminar la empresa.',
            'password.current_password' => 'La contraseña es incorrecta. La empresa no se eliminó.',
        ]);

        $name       = $company->name;
        $userIds    = $company->users()->pluck('id');
        $requestIds = VerificationRequest::whereIn('user_id', $userIds)->pluck('id');
        $paths      = Document::whereIn('request_id', $requestIds)->pluck('file_path');

        // El orden importa por las claves foráneas: solicitudes -> usuarios -> empresa
        DB::transaction(function () use ($company, $userIds) {
            VerificationRequest::whereIn('user_id', $userIds)->delete(); // servicios y documentos caen por cascada
            $company->users()->delete();
            $company->delete();
        });

        // Los archivos se borran DESPUÉS de confirmar la eliminación en la BD
        foreach ($paths as $path) Storage::disk('local')->delete($path);

        return redirect()->route('admin.companies.index')
            ->with('status', 'Empresa "' . $name . '" eliminada junto con sus usuarios y solicitudes.');
    }

    // Reglas de validación (las usan registrar y editar)
    private function companyRules(?int $ignoreId = null): array
    {
        return [
            'ruc'        => ['required', 'digits:11', Rule::unique('companies', 'ruc')->ignore($ignoreId)],
            'legal_name' => ['required', 'string', 'max:255'],
            'trade_name' => ['required', 'string', 'max:255'],
            'address'    => ['nullable', 'string', 'max:255'],
            'phone'      => ['nullable', 'digits_between:7,15'],
        ];
    }

    private function companyMessages(): array
    {
        return [
            'ruc.required'         => 'El RUC es obligatorio.',
            'ruc.digits'           => 'El RUC debe tener exactamente 11 dígitos.',
            'ruc.unique'           => 'Ya existe una empresa registrada con ese RUC.',
            'legal_name.required'  => 'La razón social es obligatoria.',
            'trade_name.required'  => 'El nombre comercial es obligatorio.',
            'phone.digits_between' => 'El teléfono debe tener entre 7 y 15 dígitos.',
        ];
    }
}