{{-- Módulo en desarrollo: pantalla provisional para que el enlace del menú ya exista. --}}
@extends('layouts.user')
@section('title', 'Perfil de Empresa - SmarTalent')
@section('page-title', 'Perfil de la Empresa')

@section('content')

{{-- 
    NOTA BACKEND: 
    - Se conecta a la ruta: route('user.profile.update') (Método PUT/PATCH).
    - Gestiona específicamente la información corporativa, fiscal y de representación legal del cliente.
--}}
<form action="{{ route('user.profile.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle teal"><i class="fas fa-building"></i></span>
            Datos Corporativos y Fiscales
        </h5>

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold small">Razón Social</label>
                <input type="text" class="form-control" name="company_name" value="{{ auth()->user()->company_name ?? 'Corporación Ejemplo S.A.C.' }}" required>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold small">RUC</label>
                <input type="text" class="form-control" name="ruc" value="{{ auth()->user()->ruc ?? '20601234567' }}" maxlength="11" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Representante Legal / Contacto Principal</label>
                <input type="text" class="form-control" name="representative" value="{{ auth()->user()->name ?? '' }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-semibold small">Correo Corporativo de Notificaciones</label>
                <input type="email" class="form-control" name="email" value="{{ auth()->user()->email ?? '' }}" required>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold small">Dirección Fiscal de la Empresa</label>
                <input type="text" class="form-control" name="address" value="{{ auth()->user()->address ?? 'Av. Javier Prado Este 1238, San Isidro, Lima' }}">
            </div>
        </div>

        <div class="mt-4 text-end">
            <button type="submit" class="btn-submit">
                <i class="fas fa-check me-1"></i> Actualizar Datos de Empresa
            </button>
        </div>
    </div>
</form>

@endsection