<header class="topbar">
    <div class="topbar-left">
        <button type="button" id="btnToggleSidebar" class="btn-sidebar-toggle" title="Menú">
            <i class="fas fa-bars"></i>
        </button>
        <a href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.dashboard') }}" class="topbar-brand">
            <img src="https://www.stgconsultoria.com/imagenes/logo1.png" alt="SmarTalent Group" class="topbar-logo">
        </a>
        <h5 id="pageTitle" class="page-title mb-0 ms-2">
            <i class="fas {{ auth()->check() && auth()->user()->role === 'admin' ? 'fa-shield-halved' : 'fa-user' }} me-2" style="color:var(--teal)"></i>
            @yield('page-title', auth()->check() && auth()->user()->role === 'admin' ? 'Panel de Administración' : 'Portal de Usuario')
        </h5>
    </div>

    <div class="topbar-right d-flex align-items-center gap-3">
        <!-- BOTÓN MODO OSCURO -->
        <button type="button" id="btnThemeToggle" class="dark-mode-btn d-flex align-items-center gap-2">
            <i id="themeIcon" class="fa-solid fa-moon"></i>
            <span id="themeText">Oscuro</span>
        </button>

        <!-- MENÚ DESPLEGABLE DE PERFIL Y LOGOUT -->
        <div class="dropdown">
            <button class="btn btn-profile-dropdown dropdown-toggle d-flex align-items-center gap-2" 
                    type="button" 
                    id="userProfileDropdown" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false">
                <i class="fa-solid {{ auth()->check() && auth()->user()->role === 'admin' ? 'fa-user-shield' : 'fa-user' }} fs-5"></i>
                <span class="fw-semibold">{{ auth()->user()->name ?? 'Usuario' }}</span>
            </button>
            
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userProfileDropdown">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('admin.profile') : '#' }}">
                        <i class="fa-solid fa-id-card text-muted"></i> Mi Perfil
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ auth()->check() && auth()->user()->role === 'admin' ? route('admin.configuration') : '#' }}">
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