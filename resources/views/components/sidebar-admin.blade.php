<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
            <i class="fas fa-th-large"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
        <a href="{{ route('admin.requests') }}" class="{{ request()->routeIs('admin.requests') ? 'active' : '' }}" title="Solicitudes">
            <i class="fas fa-list-alt"></i>
            <span class="sidebar-text">Solicitudes</span>
        </a>
        <a href="{{ route('admin.companies.index') }}" class="{{ request()->routeIs('admin.companies.*') ? 'active' : '' }}" title="Gestión de Lotes">
            <i class="fas fa-folder-open"></i>
            <span class="sidebar-text">Gestión de Lotes</span>
        </a>
        <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}" title="Reportes">
            <i class="fas fa-chart-pie"></i>
            <span class="sidebar-text">Reportes</span>
        </a>
        <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}" title="Usuarios">
            <i class="fas fa-users-cog"></i>
            <span class="sidebar-text">Usuarios</span>
        </a>
        <a href="{{ route('admin.configuration') }}" class="{{ request()->routeIs('admin.configuration') ? 'active' : '' }}" title="Configuración">
            <i class="fas fa-cog"></i>
            <span class="sidebar-text">Configuración</span>
        </a>
        <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}" title="Mi Perfil">
            <i class="fas fa-user-circle"></i>
            <span class="sidebar-text">Mi Perfil</span>
        </a>

        <!-- Soporte fijado al fondo -->
        <a href="{{ route('admin.support') }}" class="{{ request()->routeIs('admin.support') ? 'active' : '' }} nav-item-bottom" title="Soporte">
            <i class="fas fa-headset"></i>
            <span class="sidebar-text">Soporte</span>
        </a>
    </nav>
</aside>