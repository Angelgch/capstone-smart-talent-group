<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Usuario - SmarTalent Group</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Vite Directiva para CSS y JS (CAMBIADO A USER) -->
    @vite(['resources/css/user.css', 'resources/js/user.js'])
</head>
<body>

    <!-- Navbar -->
    <nav class="top-navbar">
        <div class="nav-brand">
            <img src="https://www.stgconsultoria.com/imagenes/logo1.png" alt="SmarTalent Group">
            <div class="nav-brand-text">
                <h6>SmarTalent Group</h6>
                <small>PORTAL DEL USUARIO</small>
            </div>
        </div>
        <div class="nav-right">
            <button id="darkModeToggle" class="dark-mode-btn me-3"><i class="fas fa-moon"></i> Modo Oscuro</button>
            <button class="btn-logout" onclick="logout()">
                <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
            </button>
        </div>
    </nav>

    <div class="page-container">

        <!-- Banner de Bienvenida -->
        <div class="welcome-banner">
            <div>
                <h4><i class="fas fa-hand-wave me-2"></i>¡Bienvenido al Portal!</h4>
                <p>Gestiona tus solicitudes de verificación y descarga tus documentos.</p>
            </div>
            <i class="fas fa-user-shield"></i>
        </div>

        <!-- Pestañas (Custom Tabs) -->
        <div class="custom-tabs">
            <div class="custom-tab active" onclick="switchTab('newReq', this)">
                <i class="fas fa-plus-circle"></i>
                Nueva Solicitud
            </div>
            <div class="custom-tab" onclick="switchTab('history', this); loadHistory()">
                <i class="fas fa-clipboard-list"></i>
                Mis Solicitudes
            </div>
        </div>

        <!-- Pestaña 1: Nueva Solicitud -->
        <div id="tab-newReq">
            <div class="section-card">
                <h5>
                    <span class="icon-circle teal"><i class="fas fa-user-plus"></i></span>
                    Datos del Candidato
                </h5>
                <form id="requestForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="dni" pattern="\d{8}" maxlength="8" required placeholder="DNI">
                                <label><i class="fas fa-id-card me-1"></i>DNI (8 dígitos)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="names" required placeholder="Nombres">
                                <label><i class="fas fa-user me-1"></i>Nombres</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="surnames" required placeholder="Apellidos">
                                <label><i class="fas fa-user me-1"></i>Apellidos</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" required placeholder="Email">
                                <label><i class="fas fa-envelope me-1"></i>Correo Electrónico</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="tel" class="form-control" id="phone" required placeholder="Teléfono">
                                <label><i class="fas fa-phone me-1"></i>Teléfono</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5>
                        <span class="icon-circle orange"><i class="fas fa-concierge-bell"></i></span>
                        Seleccione Servicios
                        <small class="text-muted fw-normal" style="font-size:0.8rem">(mínimo 1)</small>
                    </h5>

                    <div class="row g-3 mb-4" id="servicesCheckboxes">
                        <!-- Servicios (Se mantienen intactos) -->
                        <div class="col-md-4">
                            <div class="service-check">
                                <label>
                                    <span class="check-icon"><i class="fas fa-check" style="font-size:0.7rem"></i></span>
                                    <input type="checkbox" value="Antecedentes Nacionales"> Antecedentes Nacionales
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="service-check">
                                <label>
                                    <span class="check-icon"><i class="fas fa-check" style="font-size:0.7rem"></i></span>
                                    <input type="checkbox" value="Verificaciones Crediticias"> Verif. Crediticias
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="service-check">
                                <label>
                                    <span class="check-icon"><i class="fas fa-check" style="font-size:0.7rem"></i></span>
                                    <input type="checkbox" value="Verificaciones Laborales"> Verif. Laborales
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="service-check">
                                <label>
                                    <span class="check-icon"><i class="fas fa-check" style="font-size:0.7rem"></i></span>
                                    <input type="checkbox" value="Récord Laboral"> Récord Laboral
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="service-check">
                                <label>
                                    <span class="check-icon"><i class="fas fa-check" style="font-size:0.7rem"></i></span>
                                    <input type="checkbox" value="Verificaciones Académicas"> Verif. Académicas
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="service-check">
                                <label>
                                    <span class="check-icon"><i class="fas fa-check" style="font-size:0.7rem"></i></span>
                                    <input type="checkbox" value="Verificaciones Domiciliarias"> Verif. Domiciliarias
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="service-check">
                                <label>
                                    <span class="check-icon"><i class="fas fa-check" style="font-size:0.7rem"></i></span>
                                    <input type="checkbox" value="Ficha RENIEC"> Ficha RENIEC
                                </label>
                            </div>
                        </div>
                    </div>

                    <h5>
                        <span class="icon-circle pink"><i class="fas fa-comment-dots"></i></span>
                        Observaciones
                    </h5>
                    <div class="form-floating mb-4">
                        <textarea class="form-control" id="observations" rows="3" style="height:100px" placeholder="Observaciones"></textarea>
                        <label>Notas adicionales (opcional)</label>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane me-2"></i>Enviar Solicitud
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Pestaña 2: Historial de Solicitudes -->
        <div id="tab-history" style="display:none">
            <div id="historyContainer">
                <div class="empty-state">
                    <i class="fas fa-inbox d-block"></i>
                    <p>Cargando solicitudes...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay de Confirmación Exitosa -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-box">
            <i class="fas fa-check-circle"></i>
            <h5>¡Solicitud Creada!</h5>
            <p>Tu solicitud ha sido registrada exitosamente. Puedes hacer seguimiento en "Mis Solicitudes".</p>
            <button class="btn-submit mt-2" onclick="closeSuccess()">
                <i class="fas fa-thumbs-up me-1"></i>Entendido
            </button>
        </div>
    </div>

    <!-- Notificaciones Toast -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast align-items-center border-0" id="appToast" role="alert">
            <div class="d-flex">
                <div class="toast-body" id="toastMsg"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>