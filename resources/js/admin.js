/* ==========================================================================
admin.js — Lógica de las páginas del admin
(Sidebar, tema oscuro y devAlert viven en navigationAdmin.js)
========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    initEditForm();
    initDashboardFilter();
});

/* 1. PÁGINA EDIT: Guardar (borrador, aún sin BD) */
function initEditForm() {
    const form = document.getElementById('editForm');
    if (!form) return;

    const btnSave = document.getElementById('btnSaveEdit');
    if (!btnSave) return;

    btnSave.addEventListener('click', () => {
        alert('✅ Cambios guardados (borrador, aún sin base de datos)');
        window.location.href = form.dataset.return;
    });
}

/* 2. DASHBOARD: filtra las filas visibles por RUC/empresa y estado */
function initDashboardFilter() {
    const search = document.getElementById('dashSearch');
    const status = document.getElementById('dashStatus');
    if (!search || !status) return;

    const rows = document.querySelectorAll('.dash-row');

    const apply = () => {
        const q = search.value.trim().toLowerCase();
        const st = status.value;
        rows.forEach(row => {
            const okText = row.dataset.search.includes(q);
            const okStatus = !st || row.dataset.status === st;
            row.style.display = okText && okStatus ? '' : 'none';
        });
    };

    search.addEventListener('input', apply);
    status.addEventListener('change', apply);
}

/* 3. ACCESIBILIDAD (funciones globales que usa el widget flotante) */
let currentZoom = 100;

window.adjustFontSize = function (delta) {
    const next = currentZoom + delta * 10;
    if (next >= 80 && next <= 130) {
        currentZoom = next;
        document.body.style.zoom = currentZoom + '%';
    }
};

window.toggleDyslexicFont = function () {
    document.body.classList.toggle('font-dyslexic');
};

window.toggleTextSpacing = function () {
    document.body.classList.toggle('wide-spacing');
};

window.toggleHighContrast = function () {
    document.documentElement.classList.toggle('high-contrast');
};

window.setDaltonism = function (type) {
    const root = document.documentElement;
    const filters = ['filter-grayscale', 'filter-deuteranopia', 'filter-protanopia'];
    const target = 'filter-' + type;
    const wasActive = root.classList.contains(target);

    root.classList.remove(...filters);
    if (!wasActive) root.classList.add(target); // segundo clic lo desactiva
};

window.resetAccessibility = function () {
    currentZoom = 100;
    document.body.style.zoom = '100%';
    document.body.classList.remove('font-dyslexic', 'wide-spacing');
    document.documentElement.classList.remove(
        'high-contrast', 'filter-grayscale', 'filter-deuteranopia', 'filter-protanopia'
    );
};