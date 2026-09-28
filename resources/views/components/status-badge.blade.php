@props(['status' => null])

@php
$status = $status ?: 'No Solicitado';

$map = [
    'No Solicitado' => 'badge-no-solicitado',
    'En Proceso'    => 'badge-progreso',
    'Realizado'     => 'badge-realizado',
    'Cancelado'     => 'badge-cancelado',
];
$class = $map[$status] ?? 'badge-no-solicitado';
@endphp

<span class="badge-status {{ $class }}">{{ $status }}</span>