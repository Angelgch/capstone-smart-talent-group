<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración - SmarTalent')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.ico') }}">

    <!-- ACCESIBILIDAD INICIO-->
    <script>
    (function () {
        try {
            var p = JSON.parse(localStorage.getItem('a11y_admin') || '{}'), h = document.documentElement;   // en admin: 'a11y_admin'
            if (p.size && p.size !== 100) h.style.fontSize = p.size + '%';
            var map = { dyslexic: 'a11y-dyslexic', spacing: 'a11y-spacing', contrast: 'a11y-contrast',
                        grayscale: 'a11y-grayscale', links: 'a11y-links', motion: 'a11y-reduce-motion' };
            for (var k in map) if (p[k]) h.classList.add(map[k]);
        } catch (e) {}
    })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/opendyslexic@1.0.3/opendyslexic-regular.css">
    <!-- A -->

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

    @vite([
        'resources/css/admin.css', 
        'resources/js/admin.js', 
        'resources/js/navigationAdmin.js',
        'resources/js/accessibility-admin.js',   // <-- nueva
    ])

</head>
<body>
    <a class="skip-link" href="#mainContent">Saltar al contenido</a>
    @include('components.navigationAdmin')

    <div class="app-container">
        <main class="main-content" id="mainContent" tabindex="-1">
            <div class="p-4 flex-grow-1">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>