{{-- Configuración (estilo Twitter). Por ahora solo "Tu cuenta"; el resto es un adelanto visual. --}}
@extends('layouts.user')
@section('title', 'Configuración')
@section('page-title', 'Configuración')

@section('content')

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<div class="row g-4">
    {{-- Menú de la configuración --}}
    <div class="col-lg-3">
        <div class="list-group">
            <span class="list-group-item active"><i class="fas fa-user me-2"></i>Tu cuenta</span>
            <span class="list-group-item disabled"><i class="fas fa-shield-halved me-2"></i>Privacidad y seguridad <small class="d-block">Próximamente</small></span>
            <span class="list-group-item disabled"><i class="fas fa-bell me-2"></i>Notificaciones <small class="d-block">Próximamente</small></span>
            <span class="list-group-item disabled"><i class="fas fa-circle-question me-2"></i>Centro de ayuda <small class="d-block">Próximamente</small></span>
        </div>
    </div>

    {{-- Tu cuenta --}}
    <div class="col-lg-9">

        {{-- 1. Información de la cuenta --}}
        <div class="section-card mb-3">
            <h5 class="section-title">
                <span class="icon-circle teal"><i class="fas fa-id-card"></i></span>
                Información de la cuenta
            </h5>
            <p class="text-muted small">Nombres, apellidos, correo y teléfono.</p>
            <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary btn-sm">Editar información</a>
        </div>

        {{-- 2. Cambiar contraseña --}}
        <div class="section-card mb-3">
            <h5 class="section-title">
                <span class="icon-circle orange"><i class="fas fa-key"></i></span>
                Cambiar contraseña
            </h5>
            <form action="{{ route('user.password.update') }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')
                <div class="col-md-4">
                    <label class="form-label fw-semibold small" for="current_password">Contraseña actual</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small" for="password">Nueva contraseña (mín. 8)</label>
                    <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small" for="password_confirmation">Confirmar nueva</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn-submit"><i class="fas fa-save me-1"></i> Actualizar contraseña</button>
                </div>
            </form>
        </div>

        {{-- 3. Eliminar cuenta (pide la contraseña) --}}
        <div class="section-card border border-danger-subtle">
            <h5 class="section-title">
                <span class="icon-circle pink"><i class="fas fa-user-slash"></i></span>
                Eliminar cuenta
            </h5>
            <p class="text-muted small">
                Se borrará tu cuenta junto con <strong>todas tus solicitudes y sus documentos</strong>. Esta acción no se puede deshacer.
            </p>
            <form action="{{ route('user.account.destroy') }}" method="POST" class="row g-3 align-items-end"
                  onsubmit="return confirm('¿Seguro que quieres eliminar tu cuenta definitivamente?')">
                @csrf
                @method('DELETE')
                <div class="col-md-6">
                    <label class="form-label fw-semibold small" for="delete_password">Ingresa tu contraseña para confirmar</label>
                    <input type="password" class="form-control" id="delete_password" name="password" autocomplete="current-password" required>
                </div>
                <div class="col-md-6 text-end">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Eliminar mi cuenta</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection