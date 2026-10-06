{{-- Mi Perfil: información de la cuenta. Guarda en user.profile.update (PUT). --}}
@extends('layouts.user')
@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')

@section('content')
@php $user = auth()->user(); @endphp

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form action="{{ route('user.profile.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        {{-- Izquierda: avatar por iniciales y rol --}}
        <div class="col-lg-4 mb-4">
            <div class="section-card text-center p-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=30C1AC&color=fff&size=120"
                     alt="Avatar" class="rounded-circle shadow-sm mb-3" width="100">
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <span class="badge bg-primary text-uppercase px-3 py-1">Cliente</span>
                <p class="text-muted small mt-2 mb-0">{{ $user->company->name ?? "" }}</p>
            </div>
        </div>

        {{-- Derecha: datos de la cuenta --}}
        <div class="col-lg-8">
            <div class="section-card">
                <h5 class="section-title">
                    <span class="icon-circle teal"><i class="fas fa-user-edit"></i></span>
                    Información de la cuenta
                </h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small" for="names">Nombres</label>
                        <input type="text" class="form-control" id="names" name="names" required value="{{ old('names', $user->names) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small" for="surnames">Apellidos</label>
                        <input type="text" class="form-control" id="surnames" name="surnames" required value="{{ old('surnames', $user->surnames) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small" for="email">Correo electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small" for="phone">Teléfono (9 dígitos)</label>
                        <input type="text" class="form-control" id="phone" name="phone" maxlength="9" inputmode="numeric" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">DNI</label>
                        <input type="text" class="form-control" value="{{ $user->dni }}" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Empresa (RUC)</label>
                        <input type="text" class="form-control" value="{{ $user->company->name ?? '' }} · {{ $user->company->ruc ?? '' }}" disabled>
                    </div>
                </div>
                <small class="text-muted d-block mt-2">El DNI y la empresa no se pueden cambiar desde aquí.</small>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn-submit"><i class="fas fa-save me-1"></i> Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection