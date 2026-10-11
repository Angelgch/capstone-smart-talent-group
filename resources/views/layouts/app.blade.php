<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SmarTalent Group')</title>

    <!-- Script Anti-Parpadeo (Tema Oscuro + Sidebar Colapsado) -->
    <script>
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.setAttribute("data-theme", "dark");
        }
        if (localStorage.getItem("sidebar_collapsed") === "true") {
            document.documentElement.classList.add("sidebar-is-collapsed");
        }
    </script>

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/opendyslexic@1.0.3/opendyslexic-regular.css">

    <!-- Carga de CSS/JS dinámico según el rol del usuario -->
    @if(auth()->check() && auth()->user()->role === 'admin')
        @vite(['resources/css/admin.css', 'resources/js/admin/admin.js'])
    @else
        @vite(['resources/css/user.css', 'resources/js/user/user.js'])
    @endif
</head>
<body class="{{ session('sidebar_collapsed', false) ? 'sidebar-is-collapsed' : '' }}">

    <!-- NAVBAR SUPERIOR COMÚN -->
    @include('components.navbar')

    <!-- CONTENEDOR PRINCIPAL -->
    <div class="app-container">
        
        <!-- SIDEBAR CONDICIONAL SEGÚN EL ROL -->
        @if(auth()->check() && auth()->user()->role === 'admin')
            @include('components.sidebar-admin')
        @else
            @include('components.sidebar-user')
        @endif

        <!-- CONTENIDO DINÁMICO -->
        <main class="main-content" id="mainContent">
            <div class="p-4 flex-grow-1">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- WIDGET FLOTANTE DE ACCESIBILIDAD (Heredado de Admin) -->
    <div class="position-fixed bottom-0 end-0 p-3 z-3">
        <div class="dropup">
            <button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" 
                    type="button" id="btnAccessibility" data-bs-toggle="dropdown" aria-expanded="false"
                    style="width: 48px; height: 48px;" title="Opciones de Accesibilidad">
                <i class="fas fa-universal-access fs-5"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 mb-2" aria-labelledby="btnAccessibility" style="min-width: 220px;">
                <li class="dropdown-header fw-bold text-uppercase fs-7">Tamaño de Texto</li>
                <li><button type="button" class="dropdown-item py-1" onclick="adjustFontSize(1)"><i class="fas fa-plus me-2"></i>Aumentar texto</button></li>
                <li><button type="button" class="dropdown-item py-1" onclick="adjustFontSize(-1)"><i class="fas fa-minus me-2"></i>Reducir texto</button></li>
                <li><hr class="dropdown-divider my-1"></li>
                <li><button type="button" class="dropdown-item text-danger py-1" onclick="resetAccessibility()"><i class="fas fa-undo me-2"></i>Restablecer Todo</button></li>
            </ul>
        </div>
    </div>

    <!-- OVERLAY DE CONFIRMACIÓN EXITOSA (Heredado de User) -->
    <div class="success-overlay" id="successOverlay" style="display: none;">
        <div class="success-box">
            <i class="fas fa-check-circle"></i>
            <h5>¡Acción Exitosa!</h5>
            <p id="successMessage">Tu solicitud ha sido registrada exitosamente.</p>
            <button class="btn btn-submit mt-2" onclick="closeSuccess()">Entendido</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>