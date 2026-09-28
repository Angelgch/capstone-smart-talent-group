document.addEventListener('DOMContentLoaded', () => {
    initRequestForm();
    initDashboardFilter();
});

/* Formulario de solicitud (create / edit): valida y simula el guardado */
function initRequestForm() {
    const form = document.getElementById('requestForm');
    const btn = document.getElementById('btnSubmitRequest');
    const error = document.getElementById('formError');
    if (!form || !btn) return;

    btn.addEventListener('click', () => {
        error.classList.add('d-none');

        if (!form.reportValidity()) return; // validaciones nativas (DNI, correo, etc.)

        const checked = form.querySelectorAll('#servicesCheckboxes input:checked').length;
        if (checked === 0) {
            error.textContent = 'Seleccione al menos un servicio.';
            error.classList.remove('d-none');
            error.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        alert(form.dataset.message);
        window.location.href = form.dataset.return;
    });
}

/* Dashboard: filtra las filas visibles por DNI/nombre y estado */
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