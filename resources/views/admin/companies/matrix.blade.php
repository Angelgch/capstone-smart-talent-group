@extends('layouts.admin')
@section('title', 'Matriz de Candidatos')
@section('page-title', 'Matriz — ' . $company['name'])

@section('content')

<div class="filter-section">
    <input type="text" class="form-control" style="max-width:320px" placeholder="Buscar por DNI, nombre o apellido...">
    <select class="form-select" style="max-width:200px">
        <option>Todos los estados</option>
        <option>En Proceso</option>
        <option>Realizado</option>
        <option>Cancelado</option>
    </select>
    <button class="btn btn-gestionar"><i class="fas fa-search me-1"></i> Filtrar</button>
    <button class="btn btn-excel ms-auto"><i class="fas fa-file-excel me-1"></i> Descargar Excel</button>
</div>

<div class="table-custom-container">
    <table class="table-matrix">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombres y Apellidos</th>
                <th>Estado</th>
                @foreach ($services as $s)
                    <th>{{ $s['short'] }}</th>
                @endforeach
                <th>Dirección</th>
                <th>Referencia</th>
                <th>Observaciones</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($candidates as $c)
            <tr>
                <td>{{ $c['dni'] }}</td>
                <td>{{ $c['name'] }}</td>
                <td><x-status-badge :status="$c['status']" /></td>

                @foreach ($services as $key => $s)
                    <td><x-status-badge :status="$c['services'][$key] ?? null" /></td>
                @endforeach

                @foreach (['direccion', 'referencia', 'observaciones'] as $field)
                    <td>
                        @if ($c[$field])
                            <span class="cell-text" title="{{ $c[$field] }}">{{ $c[$field] }}</span>
                        @else
                            <x-status-badge />
                        @endif
                    </td>
                @endforeach

                <td>
                    <a href="{{ route('admin.companies.edit', [$company['id'], $c['dni']]) }}" class="btn-icon btn-icon-edit" title="Editar">
                        <i class="fas fa-pen"></i>
                    </a>
                    <a href="{{ route('admin.companies.downloads', [$company['id'], $c['dni']]) }}" class="btn-icon btn-icon-files" title="Documentos">
                        <i class="fas fa-warehouse"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection