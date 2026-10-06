{{-- resources/views/user/requests/serviceRows.blade.php
     Filas de servicios del USUARIO. Las usan "Nueva solicitud" (formCreate) y el "Detalle" (show).
     $solicitud = null al crear; con datos al editar. --}}
@php
    $lastGroup = null;
    $oldMarked = old('services');   // si el formulario volvió con errores, se respetan sus marcas
@endphp

<div class="service-rows" id="servicesCheckboxes">
    @foreach ($services as $key => $s)
        @php
            $item    = $solicitud?->item($key);        // null = servicio no pedido
            $status  = $item?->status;
            $doc     = $item?->attachment;             // documento que envió el usuario (o null)
            $locked  = $status === 'en_progreso';      // ya en trámite: no se puede quitar
            $checked = $oldMarked !== null
                ? in_array($key, $oldMarked, true)
                : ($item && $status !== 'cancelado');  // los cancelados salen desmarcados
            $choice  = old("doc_choice.$key", $doc ? 'si' : 'no');
            $group   = $s['group'] ?? null;
        @endphp

        @if ($group && $group !== $lastGroup)
            <div class="small fw-semibold text-muted mt-2">{{ $group }}</div>
        @endif
        @php $lastGroup = $group; @endphp

        <div class="service-row {{ $choice === 'si' ? 'wants-doc' : '' }}"
             data-service="{{ $key }}" data-status="{{ $status }}" data-has-doc="{{ $doc ? 1 : 0 }}">

            {{-- Columna 1: check + nombre + estado actual --}}
            <label class="service-check {{ $locked ? 'is-locked' : '' }}">
                <input type="checkbox" class="service-cb" name="services[]" value="{{ $key }}"
                       @checked($checked) @disabled($locked)>
                <span class="check-icon"><i class="fas fa-check"></i></span>
                <span>{{ $s['full'] }}</span>
                @if ($status)
                    <span class="ms-auto"><x-request-status :status="$status" /></span>
                @endif
                @if ($locked)
                    <i class="fas fa-lock ms-2 text-muted" title="Ya está en trámite: no se puede quitar"></i>
                @endif
            </label>

            {{-- Un checkbox deshabilitado no se envía: este campo mantiene el servicio en el envío --}}
            @if ($locked)
                <input type="hidden" name="services[]" value="{{ $key }}">
            @endif

            {{-- Columna 2: documento (el CSS lo muestra solo si el servicio está marcado) --}}
            <div class="service-extra">
                <div class="doc-option">
                    <span class="doc-question">¿Enviar documento?</span>
                    <div class="yes-no" role="group" aria-label="¿Enviar documento de {{ $s['full'] }}?">
                        <button type="button" class="yn-btn {{ $choice === 'no' ? 'is-active' : '' }}" data-choice="no"
                                aria-pressed="{{ $choice === 'no' ? 'true' : 'false' }}">No</button>
                        <button type="button" class="yn-btn {{ $choice === 'si' ? 'is-active' : '' }}" data-choice="si"
                                aria-pressed="{{ $choice === 'si' ? 'true' : 'false' }}">Sí</button>
                    </div>
                    {{-- "no" = sin documento (si había uno, se elimina) | "si" = tiene o sube documento --}}
                    <input type="hidden" class="doc-choice" name="doc_choice[{{ $key }}]" value="{{ $choice }}">
                </div>

                <div class="doc-upload">
                    @if ($doc)
                        <div class="small mb-1">
                            <i class="fas fa-paperclip me-1"></i>Actual:
                            <a href="{{ route('files.download', [$doc, 'view' => 1]) }}" target="_blank" rel="noopener">{{ $doc->original_name }}</a>
                        </div>
                    @endif
                    <input type="file" class="form-control form-control-sm doc-file"
                           id="doc_file_{{ $key }}" name="documents[{{ $key }}]"
                           accept=".pdf,.jpg,.jpeg,.png"
                           aria-label="{{ $doc ? 'Reemplazar' : 'Adjuntar' }} documento de {{ $s['full'] }}">
                    @if ($doc)
                        <small class="text-muted">Elige otro archivo solo si quieres reemplazar el actual.</small>
                    @endif
                    <small class="field-error d-none doc-error" role="alert"></small>
                </div>

                @if ($doc)
                    <small class="field-error d-none doc-remove-hint">Se eliminará el documento actual al guardar.</small>
                @endif
            </div>
        </div>
    @endforeach
</div>