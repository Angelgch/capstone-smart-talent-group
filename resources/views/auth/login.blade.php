@extends('layouts.auth')

@section('title', 'Smart Talent Group — Acceso')

@section('content')

    <!-- Selector Píldora (Se mantiene fijo arriba) -->
    <div class="auth-toggle-pill">
        <div class="pill-slider" id="pillSlider"></div>
        <button type="button" id="btnTabLogin" class="pill-btn active" onclick="switchTab('login')">
            <i class="fa-solid fa-right-to-bracket"></i> <span>Ingresar</span>
        </button>
        <button type="button" id="btnTabReg" class="pill-btn" onclick="switchTab('register')">
            <i class="fa-solid fa-user-plus"></i> <span>Registrarse</span>
        </button>
    </div>

    <!-- ÁREA DE FORMULARIOS (Solo esta sección cambia de altura/contenido) -->
    <div class="forms-container">

        <!-- 1. FORMULARIO DE INGRESO -->
        <div id="loginBlock" class="form-block">
            <h2>Iniciar sesión</h2>
            <p class="subtitle">Accede con tus credenciales de Smart Talent</p>

            <div id="loginError" class="alert-error"></div>

            <form id="loginForm" action="{{ route('login.attempt') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="loginEmail">Correo electrónico</label>
                    <input type="email" id="loginEmail" name="email" class="form-input"
                        placeholder="correo@empresa.com" autocomplete="username" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="loginPassword">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="loginPassword" name="password" class="form-input"
                            placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility('loginPassword')">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Ingresar al sistema</button>
            </form>
        </div>

        <!-- 2. FORMULARIO DE REGISTRO (todos los campos son obligatorios) -->
        <div id="registerBlock" class="form-block d-none">
            <h2>Crear Cuenta</h2>
            <p class="subtitle">Regístrate para solicitar evaluaciones corporativas</p>

            <div id="registerError" class="alert-error"></div>
            <div id="registerSuccess" class="alert-success">¡Registro Exitoso! Redirigiendo...</div>

            <form id="registerForm" action="{{ route('register.store') }}" method="POST" novalidate>
                @csrf
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="regRuc">RUC</label>
                        {{-- No existe token de autocompletado estándar para RUC/DNI => autocomplete="off" --}}
                        <input type="text" id="regRuc" name="ruc" class="form-input" placeholder="20123456789"
                               maxlength="11" inputmode="numeric" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="regDni">DNI</label>
                        <input type="text" id="regDni" name="dni" class="form-input" placeholder="87654321"
                               maxlength="8" inputmode="numeric" autocomplete="off" required>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="regNames">Nombres</label>
                        <input type="text" id="regNames" name="names" class="form-input" placeholder="Juan Luis"
                               autocomplete="given-name" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="regSurnames">Apellidos</label>
                        <input type="text" id="regSurnames" name="surnames" class="form-input" placeholder="Pérez López"
                               autocomplete="family-name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="regEmail">Correo electrónico</label>
                    <input type="email" id="regEmail" name="email" class="form-input"
                           placeholder="correo@empresa.com" autocomplete="email" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="regPhone">Teléfono</label>
                    <input type="tel" id="regPhone" name="phone" class="form-input" placeholder="999999999"
                           maxlength="9" inputmode="numeric" autocomplete="tel-national" required>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" for="regPassword">Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" id="regPassword" name="password" class="form-input"
                                   placeholder="••••••••" autocomplete="new-password" required>
                            <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility('regPassword')">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="regPasswordConfirm">Confirmar</label>
                        <div class="input-wrapper">
                            <input type="password" id="regPasswordConfirm" name="password_confirmation" class="form-input"
                                   placeholder="••••••••" autocomplete="new-password" required>
                            <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility('regPasswordConfirm')">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Términos y condiciones: el botón "Registrarse" queda deshabilitado hasta marcarlo (login.js).
                     Coloca el PDF real en public/docs/terminos-y-condiciones.pdf --}}
                <label class="terms-check" for="regTerms">
                    <input type="checkbox" id="regTerms" name="terms" required>
                    <span>
                        <span>Acepto los</span>
                        <a href="{{ asset('docs/terminos-y-condiciones.pdf') }}" target="_blank" rel="noopener"
                          >términos y condiciones</a>
                    </span>
                </label>

                <button type="submit" id="btnRegister" class="btn btn-primary" disabled>Registrarse</button>
            </form>
        </div>

    </div>

@endsection