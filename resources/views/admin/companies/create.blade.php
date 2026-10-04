{{-- resources/views/admin/companies/create.blade.php
     Registro de empresas (solo lo básico: RUC, razón social, nombre comercial, dirección y teléfono).
     Diseño simple a propósito: cámbialo a tu gusto, lo importante son los name="..." de los campos. --}}
@extends('layouts.admin')
@section('title', 'Nueva Empresa')
@section('page-title', 'Nueva Empresa')

@section('content')

<form method="POST" action="{{ route('admin.companies.store') }}" class="edit-card" style="max-width:720px">
    @csrf

    {{-- Errores que devuelve el servidor (ej. "Ya existe una empresa registrada con ese RUC") --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold" for="ruc">RUC (11 dígitos)</label>
            <input type="text" class="form-control" id="ruc" name="ruc" maxlength="11" inputmode="numeric"
                   autocomplete="off" required value="{{ old('ruc') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold" for="phone">Teléfono (opcional)</label>
            <input type="tel" class="form-control" id="phone" name="phone" maxlength="15" inputmode="numeric"
                   autocomplete="off" value="{{ old('phone') }}">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold" for="legal_name">Razón social</label>
            <input type="text" class="form-control" id="legal_name" name="legal_name" required value="{{ old('legal_name') }}">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold" for="trade_name">Nombre comercial</label>
            <input type="text" class="form-control" id="trade_name" name="trade_name" required value="{{ old('trade_name') }}">
        </div>
        <div class="col-12">
            <label class="form-label fw-semibold" for="address">Dirección (opcional)</label>
            <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}">
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-4">
        <a href="{{ route('admin.companies.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-gestionar"><i class="fas fa-floppy-disk me-1"></i> Registrar empresa</button>
    </div>
</form>

@endsection

@push('scripts')
<script>
    // RUC y teléfono: solo números (el servidor igual los valida)
    ['ruc', 'phone'].forEach(function (id) {
        document.getElementById(id).addEventListener('input', function (e) {
            e.target.value = e.target.value.replace(/\D/g, '');
        });
    });
</script>
@endpush
