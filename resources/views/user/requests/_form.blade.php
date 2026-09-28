@php
    $c         = $candidate ?? [];
    $isEdit    = !empty($c);
    $requested = array_keys($c['services'] ?? []);
    $locked    = ['Realizado', 'En Proceso']; // ya en trámite: no se puede quitar
@endphp

<form id="requestForm" data-return="{{ $return }}" data-message="{{ $message }}">

    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle teal"><i class="fas fa-user-plus"></i></span>
            Datos del candidato
        </h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">DNI (8 dígitos)</label>
                <input type="text" class="form-control" name="dni" pattern="\d{8}" maxlength="8" required
                       value="{{ $c['dni'] ?? '' }}" @readonly($isEdit)>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Correo electrónico</label>
                <input type="email" class="form-control" name="email" required value="{{ $c['email'] ?? '' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nombres</label>
                <input type="text" class="form-control" name="names" required value="{{ $c['names'] ?? '' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Apellidos</label>
                <input type="text" class="form-control" name="surnames" required value="{{ $c['surnames'] ?? '' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Teléfono</label>
                <input type="tel" class="form-control" name="phone" required value="{{ $c['phone'] ?? '' }}">
            </div>
        </div>
    </div>

    <div class="section-card">
        <h5 class="section-title">
            <span class="icon-circle orange"><i class="fas fa-concierge-bell"></i></span>
            Servicios a solicitar
            <small class="text-muted fw-normal" style="font-size:.8rem">(mínimo 1)</small>
        </h5>
        <div class="row g-3" id="servicesCheckboxes">
            @foreach ($services as $key => $s)
                @php $st = $c['services'][$key] ?? null; $isLocked = in_array($st, $locked); @endphp
                <div class="col-md-4">
                    <label class="service-check {{ $isLocked ? 'is-locked' : '' }}">
                        <input type="checkbox" name="services[]" value="{{ $key }}"
                               @checked(in_array($key, $requested)) @disabled($isLocked)>
                        <span class="check-icon"><i class="fas fa-check"></i></span>
                        <span>{{ $s['full'] }}</span>
                        @if ($isLocked)
                            <i class="fas fa-lock ms-auto text-muted" title="Ya está en trámite"></i>
                        @endif
                    </label>
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
                <label class="form-label fw-semibold">Dirección domiciliaria</label>
                <input type="text" class="form-control" name="direccion" value="{{ $c['direccion'] ?? '' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Referencia domiciliaria</label>
                <input type="text" class="form-control" name="referencia" value="{{ $c['referencia'] ?? '' }}">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Observaciones a tener en cuenta</label>
                <textarea class="form-control" name="observaciones" rows="3">{{ $c['observaciones'] ?? '' }}</textarea>
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