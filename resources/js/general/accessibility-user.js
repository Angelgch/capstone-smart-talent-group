// resources/js/general/accessibility-user.js
// Panel de accesibilidad (botón flotante abajo a la derecha) para el USUARIO.
// No necesita HTML en el layout: el botón se crea solo. Las preferencias se guardan en el navegador (localStorage).
//
// Opciones: tamaño de texto, fuente para dislexia, espaciado de texto, alto contraste,
//           escala de grises, resaltar enlaces y reducir animaciones.

const STORAGE_KEY = 'a11y_user';   // cada rol guarda sus preferencias por separado
const SIZE_MIN = 90, SIZE_MAX = 150, SIZE_STEP = 10;

// preferencia -> clase que se pone en <html> (el CSS está en admin.css, sección ACCESIBILIDAD)
const CLASSES = {
    dyslexic:  'a11y-dyslexic',
    spacing:   'a11y-spacing',
    contrast:  'a11y-contrast',
    grayscale: 'a11y-grayscale',
    links:     'a11y-links',
    motion:    'a11y-reduce-motion',
};
const LABELS = {
    dyslexic:  'Fuente para dislexia',
    spacing:   'Espaciado de texto',
    contrast:  'Alto contraste',
    grayscale: 'Escala de grises',
    links:     'Resaltar enlaces',
    motion:    'Reducir animaciones',
};
const DEFAULTS = { size: 100, dyslexic: false, spacing: false, contrast: false, grayscale: false, links: false, motion: false };

let prefs = { ...DEFAULTS, ...read() };

function read() {
    try { return JSON.parse(localStorage.getItem(STORAGE_KEY)) || {}; } catch { return {}; }
}

function save() {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs)); } catch { /* sin almacenamiento: no pasa nada */ }
}

// Aplica las preferencias a la página y refleja el estado en los botones (aria-pressed)
function apply() {
    const html = document.documentElement;
    html.style.fontSize = prefs.size === 100 ? '' : prefs.size + '%';

    for (const [key, cls] of Object.entries(CLASSES)) {
        html.classList.toggle(cls, !!prefs[key]);
    }

    document.querySelectorAll('#a11yWidget [data-a11y]').forEach((btn) => {
        const key = btn.dataset.a11y;
        if (key in CLASSES) btn.setAttribute('aria-pressed', String(!!prefs[key]));
    });

    const size = document.getElementById('a11ySize');
    if (size) size.textContent = prefs.size + '%';
}

// Mensaje para lectores de pantalla ("Alto contraste activado")
function announce(msg) {
    const live = document.getElementById('a11yLive');
    if (live) live.textContent = msg;
}

function handle(action) {
    if (action === 'size-up') {
        prefs.size = Math.min(SIZE_MAX, prefs.size + SIZE_STEP);
        announce(`Tamaño de texto ${prefs.size}%`);
    } else if (action === 'size-down') {
        prefs.size = Math.max(SIZE_MIN, prefs.size - SIZE_STEP);
        announce(`Tamaño de texto ${prefs.size}%`);
    } else if (action === 'reset') {
        prefs = { ...DEFAULTS };
        announce('Opciones de accesibilidad restablecidas');
    } else if (action in CLASSES) {
        prefs[action] = !prefs[action];
        announce(`${LABELS[action]} ${prefs[action] ? 'activado' : 'desactivado'}`);
    }
    save();
    apply();
}

function item(key, icon) {
    return `<li><button type="button" class="dropdown-item" data-a11y="${key}" aria-pressed="false">
                <i class="fas ${icon} me-2" aria-hidden="true"></i>${LABELS[key]}</button></li>`;
}

function buildWidget() {
    const root = document.createElement('div');
    root.id = 'a11yWidget';
    root.className = 'position-fixed bottom-0 end-0 p-3';
    root.style.zIndex = '1080';
    root.innerHTML = `
        <div class="dropup">
            <button type="button" id="btnAccessibility"
                    class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
                    style="width:48px;height:48px" data-bs-toggle="dropdown" data-bs-auto-close="outside"
                    aria-expanded="false" aria-label="Opciones de accesibilidad" title="Accesibilidad">
                <i class="fas fa-universal-access fs-5" aria-hidden="true"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 mb-2" aria-labelledby="btnAccessibility" style="min-width:250px">
                <li class="dropdown-header fw-bold text-uppercase">Texto</li>
                <li class="d-flex align-items-center gap-2 px-2 pb-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-a11y="size-down" aria-label="Reducir tamaño del texto">A−</button>
                    <span id="a11ySize" class="flex-fill text-center small">100%</span>
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-a11y="size-up" aria-label="Aumentar tamaño del texto">A+</button>
                </li>
                ${item('dyslexic', 'fa-font')}
                ${item('spacing', 'fa-text-width')}
                <li><hr class="dropdown-divider my-1"></li>
                <li class="dropdown-header fw-bold text-uppercase">Visión</li>
                ${item('contrast', 'fa-circle-half-stroke')}
                ${item('grayscale', 'fa-palette')}
                ${item('links', 'fa-link')}
                <li><hr class="dropdown-divider my-1"></li>
                <li class="dropdown-header fw-bold text-uppercase">Movimiento</li>
                ${item('motion', 'fa-person-walking')}
                <li><hr class="dropdown-divider my-1"></li>
                <li><button type="button" class="dropdown-item text-danger" data-a11y="reset">
                    <i class="fas fa-rotate-left me-2" aria-hidden="true"></i>Restablecer todo</button></li>
            </ul>
        </div>
        <div id="a11yLive" class="visually-hidden" role="status" aria-live="polite"></div>`;

    document.body.appendChild(root);

    // Un solo escuchador para todos los botones del panel
    root.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-a11y]');
        if (btn) handle(btn.dataset.a11y);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    buildWidget();
    apply();
});