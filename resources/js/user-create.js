// resources/js/user-create.js   (REEMPLAZA al anterior)
// Lógica del formulario de solicitud:
//   1) Documento opcional por servicio (Sí / No)
//   2) Dirección y referencia: TEXTO o PDF (al elegir uno, el otro desaparece y se vacía)
//   3) Teléfono: solo números y máximo 9 dígitos  |  DNI: solo números, máximo 8
//   4) Freno antes de enviar: si algo está mal NO se envía y se marca el error
//
// En "create" el formulario se envía DE VERDAD al servidor (POST normal). Aquí solo se valida antes.

/* ==========================================================================
   CONFIG
   ========================================================================== */
const MAX_FILE_MB = 5;
const ALLOWED_TYPES = ['application/pdf', 'image/jpeg', 'image/png'];
const PHONE_MAX_DIGITS = 9; // límite de dígitos del teléfono
const PHONE_MIN_DIGITS = 7; // mínimo aceptado (otros países pueden tener menos de 9)

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('requestForm');
    if (!form) return; // en otras páginas no hace nada

    initServiceDocs(form);
    initExtras(form);
    initPhone();
    initDni();

    // Al marcar un servicio se limpia el aviso "seleccione al menos uno"
    form.addEventListener('change', (e) => {
        if (e.target.classList.contains('service-cb')) showFormError('');
    });

    // Fase de captura (true): corre ANTES que cualquier otro click sobre el botón de enviar.
    form.addEventListener('click', guardSubmit, true);
});

/* ==========================================================================
   Utilidad: revisa un archivo. Devuelve el mensaje de error o '' si está bien
   ========================================================================== */
function fileError(file) {
    if (!ALLOWED_TYPES.includes(file.type)) return 'Formato no permitido. Usa PDF, JPG o PNG.';
    if (file.size > MAX_FILE_MB * 1024 * 1024) return `El archivo supera los ${MAX_FILE_MB} MB.`;
    return '';
}

/* ==========================================================================
   1. DOCUMENTO OPCIONAL POR SERVICIO
   ========================================================================== */
function initServiceDocs(form) {
    form.querySelectorAll('.service-row').forEach((row) => {
        const checkbox = row.querySelector('.service-cb');
        const hidden = row.querySelector('.doc-choice');   // valor "no" | "si"
        const file = row.querySelector('.doc-file');
        const buttons = row.querySelectorAll('.yn-btn');

        // Servicios sin opción de documento (ej. la edición de demostración) se saltan
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

            // "No" => se descarta cualquier archivo
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
    if (!box) return;
    box.textContent = msg;
    box.classList.toggle('d-none', !msg);
}

// Con "Sí", el archivo es obligatorio
function validateFile(row) {
    const file = row.querySelector('.doc-file').files[0];

    if (!file) {
        setFileError(row, 'Adjunta el documento o elige "No".');
        return false;
    }
    const msg = fileError(file);
    setFileError(row, msg);
    return !msg;
}

// Valida todos los servicios marcados donde eligió "Sí"
function validateDocs() {
    let ok = true;
    document.querySelectorAll('.service-row').forEach((row) => {
        const hidden = row.querySelector('.doc-choice');
        if (!hidden) return;
        const marcado = row.querySelector('.service-cb').checked;
        if (marcado && hidden.value === 'si' && !validateFile(row)) ok = false;
    });
    return ok;
}

/* ==========================================================================
   2. DIRECCIÓN Y REFERENCIA: TEXTO O PDF
   ========================================================================== */
function initExtras(form) {
    form.querySelectorAll('.extra-row').forEach((row) => {
        const textBox = row.querySelector('.extra-text');
        const fileBox = row.querySelector('.extra-file');
        const textInput = textBox.querySelector('input');
        const fileInput = fileBox.querySelector('input');
        const buttons = row.querySelectorAll('.yn-btn');

        const setMode = (mode) => {
            buttons.forEach((b) => {
                const active = b.dataset.choice === mode;
                b.classList.toggle('is-active', active);
                b.setAttribute('aria-pressed', active);
            });
            textBox.classList.toggle('d-none', mode !== 'texto');
            fileBox.classList.toggle('d-none', mode !== 'pdf');

            // Lo que se oculta se vacía: nunca viajan los dos al servidor
            if (mode === 'texto') {
                fileInput.value = '';
                setExtraError(row, '');
            } else {
                textInput.value = '';
            }
        };

        buttons.forEach((b) => b.addEventListener('click', () => setMode(b.dataset.choice)));
        fileInput.addEventListener('change', () => validateExtra(row));
    });
}

function setExtraError(row, msg) {
    const box = row.querySelector('.extra-error');
    if (!box) return;
    box.textContent = msg;
    box.classList.toggle('d-none', !msg);
}

// Aquí el archivo es opcional: solo se revisa si eligió uno
function validateExtra(row) {
    const file = row.querySelector('.extra-file input').files[0];
    if (!file) {
        setExtraError(row, '');
        return true;
    }
    const msg = fileError(file);
    setExtraError(row, msg);
    return !msg;
}

function validateExtras() {
    let ok = true;
    document.querySelectorAll('.extra-row').forEach((row) => {
        if (!validateExtra(row)) ok = false;
    });
    return ok;
}

/* ==========================================================================
   3. TELÉFONO Y DNI
   ========================================================================== */
function initPhone() {
    const input = document.getElementById('phone');
    if (!input) return;

    // Limpia lo que venga precargado (ej. "999 999 999" => "999999999")
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
    if (!input) return true;

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
function showFormError(msg) {
    const box = document.getElementById('formError');
    if (!box) return;
    box.textContent = msg;
    box.classList.toggle('d-none', !msg);
}

// Al menos un servicio marcado (solo en "create": la edición de demostración lo revisa user.js)
function validateServices(form) {
    const any = form.querySelector('.service-cb:checked');
    showFormError(any ? '' : 'Seleccione al menos un servicio.');
    return !!any;
}

function guardSubmit(e) {
    // Solo nos interesa el click en el botón de enviar
    if (!e.target.closest('#btnSubmitRequest')) return;

    const form = e.currentTarget;

    // Se ejecutan todas (sin cortar en la primera) para mostrar todos los errores a la vez
    const phoneOk = validatePhone();
    const docsOk = validateDocs();
    const extrasOk = validateExtras();
    const servicesOk = form.dataset.mode === 'create' ? validateServices(form) : true;

    if (!phoneOk || !docsOk || !extrasOk || !servicesOk) {
        e.preventDefault();   // el formulario no se envía
        e.stopPropagation();  // y ningún otro script reacciona a este click
        document.querySelector('.field-error:not(.d-none), .form-error:not(.d-none)')
            ?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    // Si todo está bien: no se hace nada y el navegador envía el formulario
}