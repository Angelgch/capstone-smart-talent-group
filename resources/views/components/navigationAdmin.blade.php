<header class="topbar">
    <div class="topbar-left">
        <button type="button" id="btnToggleSidebar" class="btn-sidebar-toggle" title="Menú">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ route('admin.dashboard') }}" class="topbar-brand">
            <img src="https://www.stgconsultoria.com/imagenes/logo1.png" alt="SmarTalent Group" class="topbar-logo">
        </a>
        <h5 id="pageTitle" class="page-title mb-0 ms-2">
            <i class="fas fa-shield-halved me-2" style="color:var(--teal)"></i>
            @yield('page-title', 'Panel de Administración')
        </h5>
    </div>

    <div class="topbar-right d-flex align-items-center gap-3">
        <button type="button" id="btnThemeToggle" class="dark-mode-btn d-flex align-items-center gap-2">
            <i id="themeIcon" class="fa-solid fa-moon"></i>
            <span id="themeText">Oscuro</span>
        </button>

        <div class="dropdown">
            <button class="btn btn-profile-dropdown dropdown-toggle d-flex align-items-center gap-2"
                    type="button"
                    id="userProfileDropdown"
                    data-bs-toggle="dropdown"
                    aria-expanded="false">
                <i class="fa-solid fa-user-shield fs-5"></i>
                <span class="fw-semibold">{{ auth()->user()->name ?? 'Administrador' }}</span>
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
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
            <i class="fas fa-th-large"></i>
            <span class="sidebar-text">Dashboard</span>
        </a>
        <a href="{{ route('admin.companies.index') }}" class="{{ request()->routeIs('admin.companies.*') ? 'active' : '' }}" title="Gestión de Empresas">
            <i class="fas fa-folder-open"></i>
            <span class="sidebar-text">Empresas</span>
        </a>
        <a href="{{ route('admin.profile') }}" title="Mi Perfil">
            <i class="fas fa-user-circle"></i>
            <span class="sidebar-text">Mi Perfil</span>
        </a>
        <a href="{{ route('admin.configuration') }}" title="Configuración">
            <i class="fas fa-cog"></i>
            <span class="sidebar-text">Configuración</span>
        </a>


    </nav>
</aside>