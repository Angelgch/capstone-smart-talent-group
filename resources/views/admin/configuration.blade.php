{{-- Módulo en desarrollo: pantalla provisional para que el enlace del menú ya exista. --}}
@extends('layouts.admin')
@section('title', 'Configuración - Administrador')
@section('page-title', 'Ajustes del Sistema y Cuenta')

@section('content')

{{-- 
    NOTA BACKEND: 
    Esta vista agrupa configuraciones globales del panel de gestión:
    - Izquierda: Cambio de credenciales (Apunta a route('admin.password.update')).
    - Derecha: Interruptores de comportamiento del sistema (Apunta a route('admin.settings.update')).
--}}
<div class="row">
    <!-- Bloque 1: Seguridad y Contraseña -->
    <div class="col-lg-6 mb-4">
        <div class="section-card h-100">
            <h5 class="section-title">
                <span class="icon-circle orange"><i class="fas fa-lock"></i></span>
                Seguridad y Contraseña
            </h5>
            
            <form action="{{ route('admin.password.update') }}" method="POST">
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
                    Actualizar Contraseña
                </button>
            </form>
        </div>
    </div>

    <!-- Bloque 2: Preferencias y Alertas Globales -->
    <div class="col-lg-6 mb-4">
        <div class="section-card h-100">
            <h5 class="section-title">
                <span class="icon-circle pink"><i class="fas fa-bell"></i></span>
                Notificaciones y Alertas del Sistema
            </h5>

            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="emailAlerts" name="email_alerts" checked>
                    <label class="form-check-label fw-semibold small pt-1" for="emailAlerts">
                        Recibir correos cuando un cliente cree una nueva solicitud
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="dailyReport" name="daily_report">
                    <label class="form-check-label fw-semibold small pt-1" for="dailyReport">
                        Enviar resumen diario de la matriz de candidatos a mi correo
                    </label>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" id="maintenanceMode" name="maintenance_mode">
                    <label class="form-check-label fw-semibold small pt-1 text-danger" for="maintenanceMode">
                        Activar modo de mantenimiento general (Bloquea portales de usuario)
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    Guardar Preferencias
                </button>
            </form>
        </div>
    </div>
</div>

@endsection