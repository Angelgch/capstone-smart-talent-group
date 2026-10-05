{{-- Módulo en desarrollo: pantalla provisional para que el enlace del menú ya exista. --}}
@extends('layouts.user')
@section('title', 'Configuración - Portal Cliente')
@section('page-title', 'Configuración de Cuenta')

@section('content')

{{-- 
    NOTA BACKEND: 
    - Izquierda: Permite al cliente modificar su contraseña de acceso (route('user.password.update')).
    - Derecha: Configura las preferencias de avisos automáticos de sus solicitudes (route('user.settings.update')).
--}}
<div class="row">
    <!-- Cambio de Contraseña -->
    <div class="col-lg-6 mb-4">
        <div class="section-card h-100">
            <h5 class="section-title">
                <span class="icon-circle orange"><i class="fas fa-key"></i></span>
                Cambiar Contraseña de Acceso
            </h5>

            <form action="{{ route('user.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Contraseña Actual</label>
                    <input type="password" class="form-control" name="current_password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Nueva Contraseña</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold small">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-outline-dark btn-sm fw-semibold">
                    Modificar Clave
                </button>
            </form>
        </div>
    </div>

    <!-- Preferencias de Alertas y Avisos -->
    <div class="col-lg-6 mb-4">
        <div class="section-card h-100">
            <h5 class="section-title">
                <span class="icon-circle pink"><i class="fas fa-envelope-open-text"></i></span>
                Preferencias de Notificación de Solicitudes
            </h5>

            <form action="{{ route('user.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="notifyStatus" name="notify_status" checked>
                    <label class="form-check-label fw-semibold small pt-1" for="notifyStatus">
                        Avisarme por correo cuando una solicitud pase a estado "Completado"
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="notifyDoc" name="notify_doc" checked>
                    <label class="form-check-label fw-semibold small pt-1" for="notifyDoc">
                        Avisarme si un documento adjunto es rechazado o requiere corrección
                    </label>
                </div>

                <div class="mt-4 pt-3">
                    <button type="submit" class="btn-submit">
                        Guardar Preferencias
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection