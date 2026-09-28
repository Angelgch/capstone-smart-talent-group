<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración - SmarTalent')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.ico') }}">

    <!-- Script Anti-Parpadeo (Tema Oscuro + Sidebar Colapsado) -->
    <script>
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.setAttribute("data-theme", "dark");
        }
        if (localStorage.getItem("sidebar_collapsed") === "true") {
            document.documentElement.classList.add("sidebar-is-collapsed");
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/admin.css', 'resources/js/admin.js', 'resources/js/navigationAdmin.js'])
</head>
<body>

    @include('components.navigationAdmin')

    <div class="app-container">
        <main class="main-content" id="mainContent">
            <div class="p-4 flex-grow-1">
                @yield('content')
            </div>
        </main>
    </div>

    <div class="position-fixed bottom-0 end-0 p-3 z-3">
    <div class="dropup">
        <button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center"
                type="button" id="btnAccessibility" data-bs-toggle="dropdown" aria-expanded="false"
                style="width:48px;height:48px;" title="Opciones de Accesibilidad">
            <i class="fas fa-universal-access fs-5"></i>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 mb-2" style="min-width:220px;">
            <li class="dropdown-header fw-bold text-uppercase">Tamaño de texto</li>
            <li><button type="button" class="dropdown-item py-1" onclick="adjustFontSize(1)"><i class="fas fa-plus me-2"></i>Aumentar</button></li>
            <li><button type="button" class="dropdown-item py-1" onclick="adjustFontSize(-1)"><i class="fas fa-minus me-2"></i>Reducir</button></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li class="dropdown-header fw-bold text-uppercase">Lectura</li>
            <li><button type="button" class="dropdown-item py-1" onclick="toggleDyslexicFont()"><i class="fas fa-font me-2"></i>Fuente dislexia</button></li>
            <li><button type="button" class="dropdown-item py-1" onclick="toggleTextSpacing()"><i class="fas fa-text-width me-2"></i>Espaciado</button></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li class="dropdown-header fw-bold text-uppercase">Visión y color</li>
            <li><button type="button" class="dropdown-item py-1" onclick="toggleHighContrast()"><i class="fas fa-adjust me-2"></i>Alto contraste</button></li>
            <li><button type="button" class="dropdown-item py-1" onclick="setDaltonism('deuteranopia')"><i class="fas fa-eye me-2"></i>Deuteranopía</button></li>
            <li><button type="button" class="dropdown-item py-1" onclick="setDaltonism('protanopia')"><i class="fas fa-eye me-2"></i>Protanopía</button></li>
            <li><button type="button" class="dropdown-item py-1" onclick="setDaltonism('grayscale')"><i class="fas fa-palette me-2"></i>Monocromático</button></li>
            <li><hr class="dropdown-divider my-1"></li>
            <li><button type="button" class="dropdown-item text-danger py-1" onclick="resetAccessibility()"><i class="fas fa-undo me-2"></i>Restablecer todo</button></li>
        </ul>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>