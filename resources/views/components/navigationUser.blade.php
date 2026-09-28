<header class="topbar">
    <div class="topbar-left">
        <button type="button" id="btnToggleSidebar" class="btn-sidebar-toggle" title="Menú">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ route('user.dashboard') }}" class="topbar-brand">
            <img src="https://www.stgconsultoria.com/imagenes/logo1.png" alt="SmarTalent Group" class="topbar-logo">
        </a>
        <h5 id="pageTitle" class="page-title mb-0 ms-2">
            <i class="fas fa-user-check me-2" style="color:var(--teal)"></i>
            @yield('page-title', 'Portal del Cliente')
        </h5>
    </div>

    <div class="topbar-right d-flex align-items-center gap-3">
        <button type="button" id="btnThemeToggle" class="dark-mode-btn d-flex align-items-center gap-2">
            <i id="themeIcon" class="fa-solid fa-moon"></i>
            <span id="themeText">Oscuro</span>
        </button>

        <div class="dropdown">
            <button class="btn btn-profile-dropdown dropdown-toggle d-flex align-items-center gap-2"
                    type="button" id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-circle-user fs-5"></i>
                <span class="fw-semibold">{{ auth()->user()->name ?? 'Usuario' }}</span>
            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userProfileDropdown">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#" onclick="devAlert(event)">
                        <i class="fa-solid fa-id-card text-muted"></i> Mi Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="#" onclick="devAlert(event)">
                        <i class="fa-solid fa-gear text-muted"></i> Configuración
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="m-0">
                        @csrf
                        <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-semibold w-100 border-0 bg-transparent">
                            <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<aside class="sidebar" id="sidebar">
    <nav class="sidebar-nav">
        <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}" title="Dashboard">
            <i class="fas fa-th-large"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
        <a href="{{ route('user.requests.index') }}"
           class="{{ request()->routeIs('user.requests.*') && !request()->routeIs('user.requests.create') ? 'active' : '' }}"
           title="Mis Solicitudes">
            <i class="fas fa-clipboard-list"></i>
            <span class="sidebar-text">Mis Solicitudes</span>
        </a>
        <a href="{{ route('user.requests.create') }}" class="{{ request()->routeIs('user.requests.create') ? 'active' : '' }}" title="Nueva Solicitud">
            <i class="fas fa-plus-circle"></i>
            <span class="sidebar-text">Nueva Solicitud</span>
        </a>
        <a href="#" onclick="devAlert(event)" title="Mi Perfil">
            <i class="fas fa-user-circle"></i>
            <span class="sidebar-text">Mi Perfil</span>
        </a>
        <a href="#" onclick="devAlert(event)" title="Configuracion">
            <i class="fas fa-cog"></i>
            <span class="sidebar-text">Configuracion</span>
        </a>

    </nav>
</aside>