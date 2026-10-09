{{-- resources/views/user/requests/formCreate.blade.php
     Formulario de NUEVA SOLICITUD: guarda de verdad en la BD (User\RequestController@store).
     Solo sirve para CREAR. El _form.blade.php anterior queda únicamente para la edición de demostración. --}}
@php
    $oldServices = old('services', []);
    $lastGroup   = null;
@endphp

<form id="requestForm" method="POST" action="{{ route('user.requests.store') }}"
      enctype="multipart/form-data" data-mode="create">
    @csrf

    {{-- Errores que devuelve el servidor (por si algo pasa el filtro del navegador) --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Revisa los datos:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 1. CANDIDATO --}}
    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle teal"><i class="fas fa-user-plus"></i></span>
            Datos del candidato
        </h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="dni">DNI (8 dígitos)</label>
                <input type="text" class="form-control" id="dni" name="dni" maxlength="8" inputmode="numeric"
                       pattern="\d{8}" autocomplete="off" required value="{{ old('dni') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="email">Correo electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required value="{{ old('email') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="names">Nombres</label>
                <input type="text" class="form-control" id="names" name="names" required value="{{ old('names') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="surnames">Apellidos</label>
                <input type="text" class="form-control" id="surnames" name="surnames" required value="{{ old('surnames') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="phone">Teléfono (máx. 9 dígitos)</label>
                <input type="tel" class="form-control" id="phone" name="phone" required maxlength="9"
                       inputmode="numeric" placeholder="987654321" value="{{ old('phone') }}">
                <small class="field-error d-none" id="phoneError" role="alert"></small>
            </div>
        </div>
    </div>

    {{-- 2. SERVICIOS (cada uno con su documento opcional Sí/No) --}}
    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle orange"><i class="fas fa-concierge-bell"></i></span>
            Servicios a solicitar
            <small class="text-muted fw-normal" style="font-size:.8rem">(mínimo 1)</small>
        </h5>

        <p class="text-muted small mb-3">
            El documento es opcional. Elige <strong>Sí</strong> solo si quieres adjuntarlo ahora (PDF, JPG o PNG, máx. 10 MB).
        </p>

        {{-- id="servicesCheckboxes": lo usan los scripts para contar los servicios marcados --}}
        <div class="service-rows" id="servicesCheckboxes">
            @foreach ($services as $key => $s)
                @php $group = $s['group'] ?? null; @endphp

                {{-- Título de grupo (ej. "Antecedentes Nacionales") cuando cambia --}}
                @if ($group && $group !== $lastGroup)
                    <div class="small fw-semibold text-muted mt-2">{{ $group }}</div>
                @endif
                @php $lastGroup = $group; @endphp

                <div class="service-row" data-service="{{ $key }}">

                    {{-- Columna 1: check + nombre --}}
                    <label class="service-check">
                        <input type="checkbox" class="service-cb" name="services[]" value="{{ $key }}"
                               @checked(in_array($key, $oldServices))>
                        <span class="check-icon"><i class="fas fa-check"></i></span>
                        <span>{{ $s['full'] }}</span>
                    </label>

                    {{-- Columna 2: documento (el CSS lo muestra solo si el servicio está marcado) --}}
                    <div class="service-extra">
                        <div class="doc-option">
                            <span class="doc-question">¿Enviar documento?</span>
                            <div class="yes-no" role="group" aria-label="¿Enviar documento de {{ $s['full'] }}?">
                                <button type="button" class="yn-btn is-active" data-choice="no" aria-pressed="true">No</button>
                                <button type="button" class="yn-btn" data-choice="si" aria-pressed="false">Sí</button>
                            </div>
                            {{-- "no" = sin documento (no se crea fila en documents) | "si" = sube archivo --}}
                            <input type="hidden" class="doc-choice" name="doc_choice[{{ $key }}]" value="no">
                        </div>

                        <div class="doc-upload">
                            <input type="file" class="form-control form-control-sm doc-file"
                                   id="doc_file_{{ $key }}" name="documents[{{ $key }}]"
                                   accept=".pdf,.jpg,.jpeg,.png" aria-label="Documento para {{ $s['full'] }}">
                            <small class="field-error d-none doc-error" role="alert"></small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="formError" class="form-error d-none" role="alert"></div>
    </div>

    {{-- 3. DIRECCIÓN Y REFERENCIA: solo texto, opcionales --}}
    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle pink"><i class="fas fa-location-dot"></i></span>
            Dirección y referencia domiciliaria
            <small class="text-muted fw-normal" style="font-size:.8rem">(opcional)</small>
        </h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="address">Dirección domiciliaria</label>
                <input type="text" class="form-control" id="address" name="address" maxlength="500" value="{{ old('address') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold" for="reference">Referencia domiciliaria</label>
                <input type="text" class="form-control" id="reference" name="reference" maxlength="500" value="{{ old('reference') }}">
            </div>
        </div>
    </div>


    {{-- 4. OBSERVACIONES: solo texto --}}
    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle teal"><i class="fas fa-comment-dots"></i></span>
            Observaciones a tener en cuenta
            <small class="text-muted fw-normal" style="font-size:.8rem">(opcional)</small>
        </h5>
        <textarea class="form-control" id="observations" name="observations" rows="3"
                  maxlength="2000">{{ old('observations') }}</textarea>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('user.requests.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" id="btnSubmitRequest" class="btn-submit">
            <i class="fas fa-paper-plane"></i> Enviar solicitud
        </button>
    </div>
</form>
