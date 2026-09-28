@extends('layouts.user')
@section('title', 'Mis Solicitudes')
@section('page-title', 'Mis Solicitudes')

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

    <div class="ms-auto d-flex gap-2">
        <a href="#" class="btn btn-excel" onclick="devAlert(event)"><i class="fas fa-file-excel me-1"></i> Descargar Excel</a>
        <a href="{{ route('user.requests.create') }}" class="btn-submit"><i class="fas fa-plus-circle"></i> Nueva solicitud</a>
    </div>
</div>

<div class="table-custom-container">
    <table class="table-matrix">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombres y Apellidos</th>
                <th>Fecha</th>
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
            @php $editable = !in_array($c['status'], ['Realizado', 'Cancelado']); @endphp
            <tr>
                <td>{{ $c['dni'] }}</td>
                <td>{{ $c['name'] }}</td>
                <td>{{ \Carbon\Carbon::parse($c['date'])->format('d/m/Y') }}</td>
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
                    @if ($editable)
                        <a href="{{ route('user.requests.edit', $c['dni']) }}" class="btn-icon btn-icon-edit" title="Corregir datos">
                            <i class="fas fa-pen"></i>
                        </a>
                    @else
                        <span class="btn-icon btn-icon-edit is-disabled" title="Ya no se puede editar">
                            <i class="fas fa-pen"></i>
                        </span>
                    @endif
                    <a href="{{ route('user.requests.downloads', $c['dni']) }}" class="btn-icon btn-icon-files" title="Documentos">
                        <i class="fas fa-warehouse"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection