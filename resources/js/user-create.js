// resources/js/user-create.js
// Lógica extra del formulario de solicitud (create / edit):
//   1) Documento opcional por servicio (Sí / No)
//   2) Teléfono: solo números y máximo 9 dígitos (sin regla de país)
//   3) DNI: solo números, máximo 8
//
// No toca user.js: se "engancha" antes del botón de enviar y, si algo está mal,
// frena el envío. Si todo está bien, deja pasar y user.js hace lo suyo (alert + redirección).

/* ==========================================================================
   CONFIG
   ========================================================================== */
const MAX_FILE_MB = 5;
const ALLOWED_TYPES = ['application/pdf', 'image/jpeg', 'image/png'];
const PHONE_MAX_DIGITS = 9; // límite de dígitos del teléfono
const PHONE_MIN_DIGITS = 7; // mínimo aceptado (otros países pueden tener menos de 9)

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('requestForm');
    if (!form) return; // en otras páginas (dashboard, etc.) no hace nada

    initServiceDocs(form);
    initPhone();
    initDni();

    // Fase de captura (true): corre ANTES que el click de user.js en el botón.
    form.addEventListener('click', guardSubmit, true);
});

/* ==========================================================================
   1. DOCUMENTO OPCIONAL POR SERVICIO
   ========================================================================== */
function initServiceDocs(form) {
    form.querySelectorAll('.service-row').forEach((row) => {
        const checkbox = row.querySelector('.service-cb');
        const hidden = row.querySelector('.doc-choice');   // valor "no" | "si" (para la BD)
        const file = row.querySelector('.doc-file');
        const buttons = row.querySelectorAll('.yn-btn');

        // Servicios bloqueados (ya en trámite) no tienen opción de documento: se saltan
        if (!hidden || !file) return;

        // Cambia entre "no" y "si": pinta el botón, muestra/oculta el archivo
        const setChoice = (value) => {
            hidden.value = value;
            buttons.forEach((b) => {
                const active = b.dataset.choice === value;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', active);
            });
            row.classList.toggle('wants-doc', value === 'si');

            // "No" => se descarta cualquier archivo (queda null/vacío)
            if (value === 'no') {
                file.value = '';
                setFileError(row, '');
            }
        };

        buttons.forEach((b) => b.addEventListener('click', () => setChoice(b.dataset.choice)));

        // Si desmarca el servicio, vuelve a "No"
        checkbox.addEventListener('change', () => {
            if (!checkbox.checked) setChoice('no');
        });

        // Valida el archivo apenas lo elige
        file.addEventListener('change', () => validateFile(row));
    });
}

function setFileError(row, msg) {
    const box = row.querySelector('.doc-error');
    box.textContent = msg;
    box.classList.toggle('d-none', !msg);
}

// Revisa que haya archivo, formato permitido y tamaño máximo
function validateFile(row) {
    const file = row.querySelector('.doc-file').files[0];

    if (!file) {
        setFileError(row, 'Adjunta el documento o elige "No".');
        return false;
    }
    if (!ALLOWED_TYPES.includes(file.type)) {
        setFileError(row, 'Formato no permitido. Usa PDF, JPG o PNG.');
        return false;
    }
    if (file.size > MAX_FILE_MB * 1024 * 1024) {
        setFileError(row, `El archivo supera los ${MAX_FILE_MB} MB.`);
        return false;
    }
    setFileError(row, '');
    return true;
}

// Valida todos los servicios marcados donde eligió "Sí"
function validateDocs() {
    let ok = true;
    document.querySelectorAll('.service-row').forEach((row) => {
        const hidden = row.querySelector('.doc-choice');
        if (!hidden) return; // fila bloqueada: sin documento
        const marcado = row.querySelector('.service-cb').checked;
        if (marcado && hidden.value === 'si' && !validateFile(row)) ok = false;
    });
    return ok;
}

/* ==========================================================================
   2. TELÉFONO (solo números, máximo 9 dígitos)
   ========================================================================== */
function initPhone() {
    const input = document.getElementById('phone');
    if (!input) return;

    // Limpia lo que venga precargado (ej. en edit: "999 999 999" => "999999999")
    input.value = cleanPhone(input.value);

    input.addEventListener('input', () => {
        input.value = cleanPhone(input.value); // quita letras/espacios y corta en 9
        showPhoneError('');
    });

    // Al salir del campo, avisa si está mal
    input.addEventListener('blur', () => {
        if (input.value) validatePhone();
    });
}

function cleanPhone(value) {
    return String(value).replace(/\D/g, '').slice(0, PHONE_MAX_DIGITS);
}

function showPhoneError(msg) {
    const input = document.getElementById('phone');
    const box = document.getElementById('phoneError');
    if (!input || !box) return;
    box.textContent = msg;
    box.classList.toggle('d-none', !msg);
    input.classList.toggle('is-invalid', !!msg);
}

function validatePhone() {
    const input = document.getElementById('phone');
    if (!input) return true; // si la página no tiene teléfono, no bloquea

    if (!input.value) {
        showPhoneError('Ingresa el teléfono del candidato.');
        return false;
    }
    if (input.value.length < PHONE_MIN_DIGITS) {
        showPhoneError(`El teléfono debe tener entre ${PHONE_MIN_DIGITS} y ${PHONE_MAX_DIGITS} dígitos.`);
        return false;
    }
    showPhoneError('');
    return true;
}

/* ==========================================================================
   3. DNI (solo números, máximo 8)
   ========================================================================== */
function initDni() {
    const dni = document.getElementById('dni');
    if (!dni) return;
    dni.addEventListener('input', () => {
        dni.value = dni.value.replace(/\D/g, '').slice(0, 8);
    });
}

/* ==========================================================================
   4. FRENO ANTES DE ENVIAR
   ========================================================================== */
function guardSubmit(e) {
    // Solo nos interesa el click en el botón de enviar
    if (!e.target.closest('#btnSubmitRequest')) return;

    // Se ejecutan ambas para mostrar todos los errores a la vez
    const phoneOk = validatePhone();
    const docsOk = validateDocs();

    if (!phoneOk || !docsOk) {
        e.preventDefault();
        e.stopPropagation(); // user.js no llega a ejecutarse
        document.querySelector('.field-error:not(.d-none)')
            ?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    // Si todo está bien, no hacemos nada: sigue el flujo normal de user.js
}