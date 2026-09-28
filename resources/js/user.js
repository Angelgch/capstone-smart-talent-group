document.addEventListener('DOMContentLoaded', async () => {
    // Inicializar Modo Oscuro desde LocalStorage
    initDarkMode();

    // Cargar historial y datos del usuario
    loadHistory();
    try {
        const res = await fetch('api/index.php?action=get_me');
        const data = await res.json();
        if(data.status === 'success' && data.data) {
            const me = data.data;
            document.getElementById('email').value = me.email || '';
            document.getElementById('phone').value = me.phone || '';
            
            if (me.name) {
                const parts = me.name.trim().split(' ');
                if (parts.length >= 3) {
                    document.getElementById('surnames').value = parts.slice(-2).join(' ');
                    document.getElementById('names').value = parts.slice(0, -2).join(' ');
                } else if (parts.length === 2) {
                    document.getElementById('surnames').value = parts[1];
                    document.getElementById('names').value = parts[0];
                } else {
                    document.getElementById('names').value = me.name;
                }
            }
        }
    } catch(e) {}

    // Configuración de checkboxes interactivos de servicios
    document.querySelectorAll('.service-check input[type="checkbox"]').forEach(cb => {
        cb.addEventListener('change', function() {
            const parent = this.closest('.service-check');
            if (this.checked) {
                parent.classList.add('checked');
            } else {
                parent.classList.remove('checked');
            }
        });
    });

    document.querySelectorAll('.service-check').forEach(div => {
        div.addEventListener('click', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'LABEL' || e.target.closest('label') || e.target.closest('.check-icon')) return;
            const cb = this.querySelector('input[type="checkbox"]');
            cb.checked = !cb.checked;
            cb.dispatchEvent(new Event('change'));
        });
    });

    // Envío del Formulario
    document.getElementById('requestForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const checked = Array.from(document.querySelectorAll('#servicesCheckboxes input:checked')).map(cb => cb.value);
        if (checked.length === 0) {
            showToast('Seleccione al menos un servicio.', false);
            return;
        }

        const payload = {
            dni: document.getElementById('dni').value,
            names: document.getElementById('names').value,
            surnames: document.getElementById('surnames').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            observations: document.getElementById('observations').value,
            services: checked
        };

        try {
            const res = await fetch('api/index.php?action=submit_request', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.status === 'error') {
                showToast(data.message, false);
            } else {
                document.getElementById('successOverlay').classList.add('show');
                document.getElementById('requestForm').reset();
                document.querySelectorAll('.service-check').forEach(el => el.classList.remove('checked'));
            }
        } catch (err) {
            showToast('Error de conexión. Verifica que el servidor esté activo.', false);
        }
    });
});

// Cambiar de Pestaña
function switchTab(tabId, el) {
    document.querySelectorAll('.custom-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tab-newReq').style.display = tabId === 'newReq' ? 'block' : 'none';
    document.getElementById('tab-history').style.display = tabId === 'history' ? 'block' : 'none';
}

// Toast Notificaciones
function showToast(msg, isSuccess = true) {
    const toast = document.getElementById('appToast');
    toast.className = `toast align-items-center border-0 text-white ${isSuccess ? 'bg-success' : 'bg-danger'}`;
    document.getElementById('toastMsg').textContent = msg;
    new bootstrap.Toast(toast, { delay: 3000 }).show();
}

// Cerrar Modal Exitoso
function closeSuccess() {
    document.getElementById('successOverlay').classList.remove('show');
    document.querySelectorAll('.custom-tab')[1].click(); // Click en la pestaña de historial
}

// Cargar Historial
async function loadHistory() {
    try {
        const res = await fetch('api/user/requests');
        if (res.status === 401 || res.status === 403) { window.location.href = 'index.php'; return; }
        const data = await res.json();

        if (!data || data.length === 0) {
            document.getElementById('historyContainer').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-folder-open d-block"></i>
                    <h6>Sin solicitudes aún</h6>
                    <p>Crea tu primera solicitud para ver el seguimiento aquí.</p>
                </div>`;
            return;
        }

        let html = '';
        data.forEach(req => {
            const badgeClass = req.global_status === 'Finalizado' ? 'badge-finalizado' : req.global_status === 'En Proceso' ? 'badge-proceso' : 'badge-pendiente';
            const totalServs = req.services.length;
            const doneServs = req.services.filter(s => s.status === 'Listo').length;
            const pct = totalServs > 0 ? Math.round((doneServs / totalServs) * 100) : 0;
            const date = new Date(req.created_at).toLocaleDateString('es-PE', { day: '2-digit', month: 'long', year: 'numeric' });

            let servicesHtml = req.services.map(s => {
                const isDone = s.status === 'Listo';
                return `
                <div class="service-item ${isDone ? 'done' : ''}">
                    <div class="name">
                        <i class="fas ${isDone ? 'fa-check-circle text-success' : 'fa-hourglass-half text-warning'}"></i>
                        ${s.service_name}
                    </div>
                    <div class="actions">
                        <span class="badge ${isDone ? 'bg-success' : 'bg-warning text-dark'}" style="border-radius:12px;padding:4px 12px;font-size:0.75rem">
                            ${s.status}
                        </span>
                        ${isDone && s.pdf_path ? `
                            <a href="api/index.php?action=download_pdf&file=${s.pdf_path}&view=1" target="_blank" class="btn-view-pdf">
                                <i class="fas fa-eye me-1"></i>Ver
                            </a>
                            <a href="api/index.php?action=download_pdf&file=${s.pdf_path}" download class="btn-download-pdf">
                                <i class="fas fa-download me-1"></i>Descargar
                            </a>
                        ` : `<small class="text-muted"><i class="fas fa-clock me-1"></i>En espera</small>`}
                    </div>
                </div>`;
            }).join('');

            html += `
            <div class="request-card history-card">
                <div class="history-header">
                    <div>
                        <h6><i class="fas fa-user me-2" style="color:var(--teal)"></i>${req.names} ${req.surnames}</h6>
                        <small class="text-muted">
                            <i class="fas fa-id-card me-1"></i>DNI: ${req.dni} &nbsp;|&nbsp;
                            <i class="fas fa-calendar me-1"></i>${date}
                        </small>
                    </div>
                    <span class="badge-status ${badgeClass}">${req.global_status}</span>
                </div>
                ${req.observations ? `<div class="observations-box">
                    <i class="fas fa-comment-dots me-1" style="color:var(--orange)"></i> ${req.observations}
                </div>` : ''}
                <div class="mb-2">
                    <small class="text-muted fw-bold">Progreso: ${doneServs} de ${totalServs} servicios completados</small>
                    <div class="progress-custom">
                        <div class="bar" style="width:${pct}%"></div>
                    </div>
                </div>
                <div class="mt-3 service-list">
                    ${servicesHtml}
                </div>
            </div>`;
        });

        document.getElementById('historyContainer').innerHTML = html;
    } catch (err) {
        showToast('Error cargando solicitudes.', false);
    }
}

// Logout
async function logout() {
    await fetch('api/index.php?action=logout');
    window.location.href = 'index.php';
}

// Inicialización de Tema Claro / Oscuro
/*function initDarkMode() {
    const btn = document.getElementById("darkModeToggle");
    const theme = localStorage.getItem("theme");
    if(theme === "dark") {
        document.documentElement.setAttribute("data-theme", "dark");
        if(btn) btn.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
    }
    
    if(btn) {
        btn.addEventListener("click", () => {
            const current = document.documentElement.getAttribute("data-theme");
            if(current === "dark") {
                document.documentElement.removeAttribute("data-theme");
                localStorage.setItem("theme", "light");
                btn.innerHTML = '<i class="fas fa-moon"></i> Modo Oscuro';
            } else {
                document.documentElement.setAttribute("data-theme", "dark");
                localStorage.setItem("theme", "dark");
                btn.innerHTML = '<i class="fas fa-sun"></i> Modo Claro';
            }
        });
    }
}*/