{{-- resources/views/requests/_detail.blade.php
     Ficha de DETALLE compartida por admin y usuario (así se mantiene en un solo lugar).
     Recibe: $solicitud, $services, $extras y (opcional) $showResponsable = true solo para el admin.
     Solo lectura por ahora; se volverá editable en el siguiente paso. --}}
@php $showResponsable = $showResponsable ?? false; @endphp

{{-- Descargas en .zip (pasan por DocumentController, que revisa quién las pide) --}}
<div class="d-flex flex-wrap justify-content-end gap-2 mb-3">
    <a href="{{ route('files.zip', [$solicitud, 'requisito_cliente']) }}" class="btn btn-excel btn-sm">
        <i class="fas fa-file-zipper me-1"></i> Archivos enviados (.zip)
    </a>
    <a href="{{ route('files.zip', [$solicitud, 'informe_admin']) }}" class="btn btn-excel btn-sm">
        <i class="fas fa-file-zipper me-1"></i> Informes (.zip)
    </a>
</div>

{{-- 1. Datos de la solicitud y del candidato --}}
<div class="edit-card mb-3">
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <small class="text-muted d-block">N° de solicitud</small>
            <strong>{{ $solicitud->code }}</strong>
        </div>
        <div class="col-6 col-md-3">
            <small class="text-muted d-block">Fecha de solicitud</small>
            {{ $solicitud->created_at->format('d/m/Y H:i') }}
        </div>
        @if ($showResponsable)
            <div class="col-6 col-md-3">
                <small class="text-muted d-block">Responsable</small>
                {{ $solicitud->user->name }}
                <small class="text-muted d-block">{{ $solicitud->user->email }}</small>
            </div>
        @endif
        <div class="col-6 col-md-3">
            <small class="text-muted d-block">Estado general</small>
            <x-request-status :status="$solicitud->status" kind="general" />
        </div>

        <div class="col-6 col-md-3">
            <small class="text-muted d-block">DNI</small>
            {{ $solicitud->dni }}
        </div>
        <div class="col-6 col-md-3">
            <small class="text-muted d-block">Candidato</small>
            {{ $solicitud->full_name }}
        </div>
        <div class="col-6 col-md-3">
            <small class="text-muted d-block">Correo</small>
            {{ $solicitud->email }}
        </div>
        <div class="col-6 col-md-3">
            <small class="text-muted d-block">Teléfono</small>
            {{ $solicitud->phone }}
        </div>
    </div>
</div>

{{-- 2. Servicios: estado + archivo del usuario + informe --}}
<div class="edit-card mb-3">
    <h6 class="fw-bold mb-3">Servicios</h6>

    @php $lastGroup = null; @endphp
    @foreach ($services as $key => $s)
        @php
            $item  = $solicitud->item($key);   // null = no solicitado
            $group = $s['group'] ?? null;
        @endphp

        @if ($group && $group !== $lastGroup)
            <div class="small fw-semibold text-muted mt-3 mb-1">{{ $group }}</div>
        @endif
        @php $lastGroup = $group; @endphp

        <div class="doc-item {{ $item ? '' : 'doc-item-off' }}">
            <div class="doc-name">{{ $s['full'] }}</div>

            <div class="doc-file">
                @if ($item)
                    <x-request-status :status="$item->status" />

                    @if ($item->attachment)
                        <div class="small text-muted mt-1">
                            <i class="fas fa-paperclip me-1"></i>Enviado:
                            <a href="{{ route('files.download', $item->attachment) }}">{{ $item->attachment->original_name }}</a>
                        </div>
                    @endif

                    @if ($item->result)
                        <div class="small mt-1"><i class="fas fa-file-pdf text-danger me-1"></i>{{ $item->result->original_name }}</div>
                    @else
                        <div class="small text-muted mt-1">Informe pendiente</div>
                    @endif
                @else
                    <span class="text-muted">No solicitado</span>
                @endif
            </div>

            <div class="doc-actions">
                @if ($item && $item->result)
                    <a href="{{ route('files.download', [$item->result, 'view' => 1]) }}" target="_blank" rel="noopener"
                       class="btn-icon btn-icon-view" title="Visualizar informe"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('files.download', $item->result) }}"
                       class="btn-icon btn-icon-files" title="Descargar informe"><i class="fas fa-download"></i></a>
                @endif
            </div>
        </div>
    @endforeach
</div>

{{-- 3. Domicilio (texto o PDF) y observaciones (solo texto) --}}
<div class="edit-card">
    <h6 class="fw-bold mb-3">Domicilio y observaciones</h6>

    @foreach ($extras as $key => $e)
        @php $item = $solicitud->item($key); @endphp
        <div class="mb-3">
            <small class="text-muted d-block">
                {{ $e['full'] }}
                @if ($item) <x-request-status :status="$item->status" /> @endif
            </small>

            @if ($item && $item->attachment)
                {{-- Eligió PDF --}}
                <i class="fas fa-file-pdf text-danger me-1"></i>{{ $item->attachment->original_name }}
                <a href="{{ route('files.download', [$item->attachment, 'view' => 1]) }}" target="_blank" rel="noopener"
                   class="btn-icon btn-icon-view ms-2" title="Visualizar"><i class="fas fa-eye"></i></a>
                <a href="{{ route('files.download', $item->attachment) }}"
                   class="btn-icon btn-icon-files" title="Descargar"><i class="fas fa-download"></i></a>
            @elseif ($item && $item->text)
                {{-- Eligió texto --}}
                {{ $item->text }}
            @else
                <span class="text-muted">No indicada</span>
            @endif
        </div>
    @endforeach

    <div>
        <small class="text-muted d-block">Observaciones a tener en cuenta</small>
        @if ($solicitud->observations)
            {{ $solicitud->observations }}
        @else
            <span class="text-muted">Sin observaciones</span>
        @endif
    </div>
</div>
