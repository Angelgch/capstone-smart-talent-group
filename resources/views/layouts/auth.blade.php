<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Smart Talent Group — Acceso')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo.ico') }}">
    <!-- Fuentes y Fuentes de Íconos -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/login.css', 'resources/js/login.js'])
    @stack('styles')
<!--Enviar los formularios a la bd  -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    if (localStorage.getItem("theme") === "dark") {
        document.documentElement.setAttribute("data-theme", "dark");
    }
</script>
<!-- Script otro -->
</head>
<body>

    <!-- Cuadro de Prueba Temporal (Fijo Superior Izquierda) -->
    <div class="demo-box-floating">
        <strong data-i18n="demo_title">Datos de Prueba:</strong><br>
        <strong>Admin:</strong> admin@gmail.com / 12345<br>
        <strong>User:</strong> user@gmail.com / 12345
    </div>

    <!-- Controles Flotantes Superior Derecho (Idioma y Tema) -->
    <div class="top-controls">
        <button id="btnThemeToggle" class="control-btn" type="button">
            <i class="fa-solid fa-moon" id="themeIcon"></i> <span id="themeText">Oscuro</span>
        </button>
    </div>

    <div class="auth-layout">

        <!-- LADO IZQUIERDO: Branding -->
        <div class="auth-sidebar">
            <div class="branding-content">
                <h2 class="slogan-title" data-i18n="slogan">CONECTA CON EL TALENTO HUMANO MEJOR CALIFICADO</h2>
                <img src="{{ asset('images/logo.png') }}" alt="Smart Talent Group" class="brand-logo-large">
            </div>
        </div>

        <!-- LADO DERECHO: aquí se inyecta el contenido de cada vista -->
        <div class="auth-content">
            <div class="auth-form-wrapper">
                @yield('content')
            </div>
        </div>

    </div>

    @stack('scripts')
</body>
</html>