{{-- resources/views/user/requests/_form.blade.php --}}
{{-- Parcial compartido por create y edit. Reemplaza tu archivo completo por este. --}}
@php
    $c         = $candidate ?? [];
    $isEdit    = !empty($c);
    $requested = array_keys($c['services'] ?? []);
    $locked    = ['Realizado', 'En Proceso']; // ya en trámite: no se puede quitar
@endphp

{{-- enctype: necesario para subir archivos cuando exista el backend. @csrf ya queda listo. --}}
<form id="requestForm" enctype="multipart/form-data" data-return="{{ $return }}" data-message="{{ $message }}">
    @csrf

    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle teal"><i class="fas fa-user-plus"></i></span>
            Datos del candidato
        </h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="dni">DNI (8 dígitos)</label>
                {{-- NUEVO: id="dni" (user-create.js lo usa para dejar solo números) + inputmode numérico --}}
                <input type="text" class="form-control" id="dni" name="dni" pattern="\d{8}" maxlength="8"
                       inputmode="numeric"  autocomplete="off" placeholder="87654321"
                       required value="{{ $c['dni'] ?? '' }}" @readonly($isEdit)>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="email">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" autocomplete="email" required value="{{ $c['email'] ?? '' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="names">Nombres</label>
                <input type="text" class="form-control" id="names" name="names" autocomplete="given-name" required value="{{ $c['names'] ?? '' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="surnames">Apellidos</label>
                <input type="text" class="form-control" id="surnames" name="surnames" autocomplete="family-name" required value="{{ $c['surnames'] ?? '' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="phone">Teléfono (máx. 9 dígitos)</label>
                {{-- Solo números y máximo 9 dígitos (el límite real lo controla user-create.js) --}}
                <input type="tel" class="form-control" id="phone" name="phone" autocomplete="tel" required
                       maxlength="9" inputmode="numeric" placeholder="987654321"
                       value="{{ $c['phone'] ?? '' }}">
                {{-- NUEVO: mensaje de error que llena user-create.js --}}
                <small class="field-error d-none" id="phoneError" role="alert"></small>
            </div>
        </div>
    </div>

    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle orange"><i class="fas fa-concierge-bell"></i></span>
            Servicios a solicitar
            <small class="text-muted fw-normal" style="font-size:.8rem">(mínimo 1)</small>
        </h5>

        <p class="text-muted small mb-3">
            El documento es opcional. Elige <strong>Sí</strong> solo si quieres adjuntarlo ahora (PDF, JPG o PNG, máx. 5 MB).
        </p>

        {{-- Se mantiene id="servicesCheckboxes": user.js lo usa para contar los servicios marcados --}}
        <div class="service-rows" id="servicesCheckboxes">
            @foreach ($services as $key => $s)
                @php $st = $c['services'][$key] ?? null; $isLocked = in_array($st, $locked); @endphp

                {{-- Cada servicio = una fila de 3 columnas (ver CSS .service-row) --}}
                <div class="service-row" data-service="{{ $key }}">

                    {{-- Columna 1: check + nombre (tu .service-check de siempre) --}}
                    <label class="service-check {{ $isLocked ? 'is-locked' : '' }}">
                        <input type="checkbox" class="service-cb" name="services[]" value="{{ $key }}"
                               @checked(in_array($key, $requested)) @disabled($isLocked)>
                        <span class="check-icon"><i class="fas fa-check"></i></span>
                        <span>{{ $s['full'] }}</span>
                        @if ($isLocked)
                            <i class="fas fa-lock ms-auto text-muted" title="Ya está en trámite"></i>
                        @endif
                    </label>

                    {{-- Los servicios bloqueados ya están en trámite: no llevan opción de documento --}}
                    @unless ($isLocked)
                        {{-- Columna 2: agrupa "¿Enviar documento?" (Sí/No) y el archivo.
                             Si no caben en una línea, el archivo baja solo (flex-wrap) sin salirse de la tarjeta --}}
                        <div class="service-extra">

                            {{-- Aparece por CSS cuando el servicio está marcado --}}
                            <div class="doc-option">
                                <span class="doc-question">¿Enviar documento?</span>
                                <div class="yes-no" role="group" aria-label="¿Enviar documento de {{ $s['full'] }}?">
                                    <button type="button" class="yn-btn is-active" data-choice="no" aria-pressed="true">No</button>
                                    <button type="button" class="yn-btn" data-choice="si" aria-pressed="false">Sí</button>
                                </div>
                                {{-- Valor para la BD: "no" => sin documento (null) | "si" => sube archivo --}}
                                <input type="hidden" class="doc-choice" name="doc_choice[{{ $key }}]" value="no">
                            </div>

                            {{-- Aparece solo si elige Sí --}}
                            <div class="doc-upload">
                                <input type="file"
                                       class="form-control form-control-sm doc-file"
                                       id="doc_file_{{ $key }}"
                                       name="documents[{{ $key }}]"
                                       accept=".pdf,.jpg,.jpeg,.png"
                                       aria-label="Documento para {{ $s['full'] }}">
                                <small class="field-error d-none doc-error" role="alert"></small>
                            </div>
                        </div>
                    @endunless
                </div>
            @endforeach
        </div>
    </div>

    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle pink"><i class="fas fa-comment-dots"></i></span>
            Datos adicionales <small class="text-muted fw-normal" style="font-size:.8rem">(opcional)</small>
        </h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="direccion">Dirección domiciliaria</label>
                <input type="text" class="form-control" id="direccion" name="direccion" autocomplete="street-address" value="{{ $c['direccion'] ?? '' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="referencia">Referencia domiciliaria</label>
                <input type="text" class="form-control" id="referencia" name="referencia" autocomplete="off" value="{{ $c['referencia'] ?? '' }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold" for="observaciones">Observaciones a tener en cuenta</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="3">{{ $c['observaciones'] ?? '' }}</textarea>
            </div>
        </div>
    </div>

    <div id="formError" class="form-error d-none"></div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ $return }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="button" id="btnSubmitRequest" class="btn-submit">
            <i class="fas {{ $isEdit ? 'fa-floppy-disk' : 'fa-paper-plane' }}"></i>
            {{ $isEdit ? 'Guardar cambios' : 'Enviar solicitud' }}
        </button>
    </div>
</form>