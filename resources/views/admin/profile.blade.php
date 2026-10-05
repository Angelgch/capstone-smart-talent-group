{{-- Módulo en desarrollo: pantalla provisional para que el enlace del menú ya exista. --}}
@extends('layouts.admin')
@section('title', 'Mi Perfil - Administrador')
@section('page-title', 'Perfil de Administrador')

@section('content')

{{-- 
    NOTA BACKEND: 
    - Este formulario apunta a la ruta: route('admin.profile.update') (Método PUT/PATCH).
    - Usa 'auth()->user()' para inyectar los datos actuales del administrador autenticado.
    - Se requiere 'enctype="multipart/form-data"' para procesar la subida del archivo de avatar.
--}}
<form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- Columna Izquierda: Avatar y Rol del Admin -->
        <div class="col-lg-4 mb-4">
            <div class="section-card text-center p-4">
                <div class="mb-3">
                    {{-- Generación dinámica de avatar por iniciales o imagen actual --}}
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin Master') }}&background=30C1AC&color=fff&size=120" 
                         alt="Avatar" class="rounded-circle shadow-sm mb-3" width="100">
                    <h5 class="fw-bold mb-1">{{ auth()->user()->name ?? 'Administrador General' }}</h5>
                    <span class="badge bg-primary text-uppercase px-3 py-1">Super Admin</span>
                </div>
                <div class="mb-3 text-start">
                    <label for="avatar" class="form-label fw-semibold small">Cambiar foto de perfil</label>
                    <input type="file" class="form-control form-control-sm" id="avatar" name="avatar">
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Datos de Identidad -->
        <div class="col-lg-8">
            <div class="section-card">
                <h5 class="section-title">
                    <span class="icon-circle teal"><i class="fas fa-user-edit"></i></span>
                    Información Personal y de Contacto
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Nombre completo</label>
                        <input type="text" class="form-control" name="name" value="{{ auth()->user()->name ?? '' }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Correo Electrónico</label>
                        <input type="email" class="form-control" name="email" value="{{ auth()->user()->email ?? '' }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Teléfono / Celular</label>
                        <input type="text" class="form-control" name="phone" value="{{ auth()->user()->phone ?? '' }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Departamento / Área</label>
                        <input type="text" class="form-control" value="Tecnología y Operaciones" disabled>
                        <small class="text-muted">Campo institucional de sistema.</small>
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection