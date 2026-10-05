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