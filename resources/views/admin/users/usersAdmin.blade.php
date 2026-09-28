@extends('layouts.admin')

@section('title', 'Clientes - SmarTalent')
@section('page-title', 'Directorio de Clientes')

@section('content')
<div class="card border-0 shadow-sm p-4">
        <h5 class="fw-semibold mb-2">🚧 Usuarios</h5>
        <p class="text-muted mb-0">Esta sección está en desarrollo.</p>
    </div>

<!-- <!-- <div class="table-custom-container">
    <table class="data-table-new" style="width: 100%;">
        <thead style="background:#fff">
            <tr>
                <th>ID</th>
                <th>NOMBRE</th>
                <th>CORREO ELECTRÓNICO</th>
                <th>TELÉFONO</th>
                <th>FECHA REGISTRO</th>
                <th>ESTADO CUENTA</th>
                <th>ACCIONES</th>
            </tr>
        </thead>
        <tbody id="clientsTableBody">
            <tr><td colspan="7" class="text-center py-4">Cargando clientes...</td></tr>
        </tbody>
    </table>
</div>

<!-- Modal Editar Cliente --
<div class="modal fade" id="editClientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Editar Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editClientForm">
                    <input type="hidden" id="ec_id">
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Nombre del Cliente</label>
                        <input type="text" class="form-control" id="ec_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Correo Electrónico</label>
                        <input type="email" class="form-control" id="ec_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted fw-bold">Teléfono</label>
                        <input type="tel" class="form-control" id="ec_phone" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-gestionar">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> -->
@endsection

@push('scripts')
<!-- <script>
let allClients = [];
let editClientModal;

document.addEventListener('DOMContentLoaded', () => {
    editClientModal = new bootstrap.Modal(document.getElementById('editClientModal'));
    loadClients();

    document.getElementById('editClientForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const payload = {
            id: document.getElementById('ec_id').value,
            name: document.getElementById('ec_name').value,
            email: document.getElementById('ec_email').value,
            phone: document.getElementById('ec_phone').value
        };
        try {
            const res = await fetch('/api/index.php?action=edit_client', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            if ((await res.json()).status === 'success') {
                editClientModal.hide();
                loadClients();
            }
        } catch(err) {}
    });
});

async function loadClients() {
    try {
        const res = await fetch('/api/index.php?action=get_clients');
        const data = await res.json();
        allClients = Array.isArray(data) ? data : [];
        renderClients();
    } catch (e) {
        document.getElementById('clientsTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Error al cargar clientes.</td></tr>';
    }
}

function renderClients() {
    const tbody = document.getElementById('clientsTableBody');
    if (allClients.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">No hay clientes registrados.</td></tr>';
        return;
    }
    tbody.innerHTML = allClients.map(c => `
        <tr>
            <td>#${c.id}</td>
            <td class="fw-bold text-dark">${c.name || 'Sin Nombre'}</td>
            <td class="text-muted">${c.email}</td>
            <td class="text-muted">${c.phone || '-'}</td>
            <td>${new Date(c.created_at).toLocaleDateString('es-PE')}</td>
            <td><span class="badge bg-success px-3 py-2">Activo</span></td>
            <td>
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-sm btn-outline-primary" onclick="openEditClient(${c.id})" title="Editar Cuenta"><i class="fas fa-edit"></i> Editar</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteClient(${c.id})" title="Eliminar Cuenta"><i class="fas fa-trash-alt"></i> Eliminar</button>
                </div>
            </td>
        </tr>
    `).join('');
}

function openEditClient(id) {
    const client = allClients.find(c => c.id === id);
    if (!client) return;
    document.getElementById('ec_id').value = client.id;
    document.getElementById('ec_name').value = client.name || '';
    document.getElementById('ec_email').value = client.email;
    document.getElementById('ec_phone').value = client.phone || '';
    editClientModal.show();
}

async function deleteClient(id) {
    if (!confirm("¿Estás seguro de eliminar este cliente? Se borrarán TODAS sus solicitudes asociadas.")) return;
    try {
        const res = await fetch('/api/index.php?action=delete_client', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        if ((await res.json()).status === 'success') loadClients();
    } catch(e) {}
}
</script> --> -->
@endpush