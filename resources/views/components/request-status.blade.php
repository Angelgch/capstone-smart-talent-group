{{-- resources/views/components/request-status.blade.php
     Etiqueta de estado. Uso:  <x-request-status :status="$r->status" kind="general" />
                               <x-request-status :status="$item->status" />   (kind="service" por defecto)
     Usa las clases dash-badge-* que ya existen en admin.css. NO reemplaza a tu <x-status-badge>. --}}
@props(['status' => null, 'kind' => 'service'])

@php
    $label = $kind === 'general'
        ? \App\Support\StatusLabel::general($status)
        : \App\Support\StatusLabel::service($status);

    $class = match ($status) {
        'en_espera'   => 'dash-badge-pendiente',
        'en_progreso' => 'dash-badge-progreso',
        'realizado'   => 'dash-badge-completado',
        'cancelado'   => 'dash-badge-cancelado',
        default       => null,
    };
@endphp

@if ($class)
    <span class="badge-status {{ $class }}">{{ $label }}</span>
@else
    <span class="text-muted small">No solicitado</span>
@endif