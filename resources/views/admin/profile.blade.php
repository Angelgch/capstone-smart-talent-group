{{-- Mi Perfil: información de la cuenta. Guarda en admin.profile.update (PUT). --}}
@extends('layouts.admin')
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

<form action="{{ route('admin.profile.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">
        {{-- Izquierda: avatar por iniciales y rol --}}
        <div class="col-lg-4 mb-4">
            <div class="section-card text-center p-4">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=30C1AC&color=fff&size=120"
                     alt="Avatar" class="rounded-circle shadow-sm mb-3" width="100">
                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <span class="badge bg-primary text-uppercase px-3 py-1">Administrador</span>

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
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn-submit"><i class="fas fa-save me-1"></i> Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection