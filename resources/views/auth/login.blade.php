@extends('layouts.auth')

@section('title', 'Smart Talent Group — Acceso')

@section('content')

    <!-- Selector Píldora (Se mantiene fijo arriba) -->
    <div class="auth-toggle-pill">
        <div class="pill-slider" id="pillSlider"></div>
        <button type="button" id="btnTabLogin" class="pill-btn active" onclick="switchTab('login')">
            <i class="fa-solid fa-right-to-bracket"></i> <span data-i18n="tab_login">Ingresar</span>
        </button>
        <button type="button" id="btnTabReg" class="pill-btn" onclick="switchTab('register')">
            <i class="fa-solid fa-user-plus"></i> <span data-i18n="tab_register">Registrarse</span>
        </button>
    </div>

    <!-- ÁREA DE FORMULARIOS (Solo esta sección cambia de altura/contenido) -->
    <div class="forms-container">

        <!-- 1. FORMULARIO DE INGRESO -->
        <div id="loginBlock" class="form-block show">
            <h2 data-i18n="login_title">Iniciar sesión</h2>
            <p class="subtitle" data-i18n="login_sub">Accede con tus credenciales de Smart Talent</p>

            <div id="loginError" class="alert-error"></div>

            <form id="loginForm">
                <div class="form-group">
                    <label class="form-label" data-i18n="email_label">Correo electrónico</label>
                    <input type="email" id="loginEmail" class="form-input" placeholder="correo@empresa.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" data-i18n="pwd_label">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" id="loginPassword" class="form-input" placeholder="••••••••" required>
                        <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility('loginPassword')">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" data-i18n="login_btn">Ingresar al sistema</button>
            </form>
        </div>

        <!-- 2. FORMULARIO DE REGISTRO -->
        <div id="registerBlock" class="form-block d-none">
            <h2 data-i18n="reg_title">Crear Cuenta</h2>
            <p class="subtitle" data-i18n="reg_sub">Regístrate para solicitar evaluaciones corporativas</p>

            <div id="registerError" class="alert-error"></div>
            <div id="registerSuccess" class="alert-success" data-i18n="reg_success">¡Registro Exitoso! Redirigiendo...</div>

            <form id="registerForm">
                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label">RUC (Opcional)</label>
                        <input type="text" id="regRuc" class="form-input" placeholder="20123456789" maxlength="11" inputmode="numeric">
                    </div>
                    <div class="form-group">
                        <label class="form-label">DNI</label>
                        <input type="text" id="regDni" class="form-input" placeholder="87654321" maxlength="8" inputmode="numeric" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" data-i18n="name_label">Nombre completo</label>
                    <input type="text" id="regName" class="form-input" placeholder="Juan Luis Pérez Lopez" required>
                </div>

                <div class="form-group">
                    <label class="form-label" data-i18n="email_label">Correo electrónico</label>
                    <input type="email" id="regEmail" class="form-input" placeholder="correo@empresa.com" required>
                </div>

                <div class="form-group">
                    <label class="form-label" data-i18n="phone_label">Teléfono</label>
                    <input type="tel" id="regPhone" class="form-input" placeholder="999 999 999" required>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label class="form-label" data-i18n="pwd_label">Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" id="regPassword" class="form-input" placeholder="••••••••" required>
                            <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility('regPassword')">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" data-i18n="confirm_pwd">Confirmar</label>
                        <div class="input-wrapper">
                            <input type="password" id="regPasswordConfirm" class="form-input" placeholder="••••••••" required>
                            <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility('regPasswordConfirm')">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" data-i18n="reg_btn">Registrarse</button>
            </form>
        </div>

    </div>

@endsection