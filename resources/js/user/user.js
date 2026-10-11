document.addEventListener('DOMContentLoaded', () => {
    initDashboardFilter();
});

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