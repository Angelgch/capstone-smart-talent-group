document.addEventListener("DOMContentLoaded", () => {
    initTheme();
    initLoginForm();
    initRegisterForm();
});

/* ==========================================================================
   CONFIG
   ========================================================================== */
const RUC_DIGITS = 11;
const DNI_DIGITS = 8;
const PHONE_MAX_DIGITS = 9; // teléfono: exactamente 9 dígitos (después se cambiará por selector de país con bandera)
const PHONE_MIN_DIGITS = 9;
const PASSWORD_MIN = 8;
const REDIRECT_DELAY_MS = 1200; // pausa para que se vea "¡Registro Exitoso!" antes de entrar

/* ==========================================================================
   COMUNICACIÓN CON LARAVEL (POST en JSON, con token CSRF)
   ========================================================================== */
async function postJson(url, payload) {
    try {
        const res = await fetch(url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content ?? "",
            },
            body: JSON.stringify(payload),
        });
        const data = await res.json().catch(() => ({}));
        return { ok: res.ok, status: res.status, data };
    } catch (e) {
        return { ok: false, status: 0, data: {} }; // sin conexión con el servidor
    }
}

// Convierte la respuesta de error del servidor en un mensaje para el usuario
function serverMessage({ status, data }) {
    if (status === 422) {
        const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
        return firstError || data.message || "Revise los datos ingresados.";
    }
    if (status === 429) return "Demasiados intentos. Espere un minuto e inténtelo de nuevo.";
    if (status === 419) return "La sesión expiró. Recargue la página e inténtelo de nuevo.";
    if (status === 0) return "No se pudo conectar con el servidor.";
    return "Ocurrió un error inesperado. Inténtelo de nuevo.";
}

/* ==========================================================================
   1. LOGIN (el servidor valida y devuelve a qué panel ir según el rol)
   ========================================================================== */
function initLoginForm() {
    const loginForm = document.getElementById("loginForm");
    if (!loginForm) return;

    const btn = loginForm.querySelector('button[type="submit"]');

    loginForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        showLoginError("");

        btn.disabled = true;
        const res = await postJson(loginForm.action, {
            email: document.getElementById("loginEmail").value.trim(),
            password: document.getElementById("loginPassword").value,
        });

        if (res.ok) {
            window.location.href = res.data.redirect; // /admin/dashboard o /user/dashboard
            return;
        }

        showLoginError(serverMessage(res));
        btn.disabled = false;
    });
}

function showLoginError(msg) {
    const box = document.getElementById("loginError");
    if (!box) return;
    box.innerText = msg;
    box.style.display = msg ? "block" : "none";
}

/* ==========================================================================
   1.b REGISTRO (valida en pantalla y luego el servidor crea el usuario)
   ========================================================================== */
function initRegisterForm() {
    const form = document.getElementById("registerForm");
    if (!form) return;

    const success = document.getElementById("registerSuccess");
    const btn = document.getElementById("btnRegister");
    const terms = document.getElementById("regTerms");

    // Solo números y límite de dígitos
    bindDigits("regRuc", RUC_DIGITS);
    bindDigits("regDni", DNI_DIGITS);
    bindDigits("regPhone", PHONE_MAX_DIGITS);

    // "Registrarse" deshabilitado hasta aceptar términos y condiciones
    const syncButton = () => { btn.disabled = !terms.checked; };
    terms.addEventListener("change", syncButton);
    syncButton();

    // Al escribir, se limpia el mensaje de error
    form.addEventListener("input", () => showRegisterError(""));

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        success.style.display = "none";

        const val = (id) => document.getElementById(id).value.trim();
        const password = document.getElementById("regPassword").value; // la contraseña no se recorta
        const passwordConfirm = document.getElementById("regPasswordConfirm").value;

        // Todos los campos son obligatorios
        const required = ["regRuc", "regDni", "regNames", "regSurnames", "regEmail", "regPhone"];
        if (required.some((id) => !val(id)) || !password || !passwordConfirm) {
            return showRegisterError("Todos los campos son obligatorios.");
        }

        if (val("regRuc").length !== RUC_DIGITS) return showRegisterError(`El RUC debe tener exactamente ${RUC_DIGITS} dígitos.`);
        if (val("regDni").length !== DNI_DIGITS) return showRegisterError(`El DNI debe tener exactamente ${DNI_DIGITS} dígitos.`);
        if (val("regPhone").length < PHONE_MIN_DIGITS) return showRegisterError(`El teléfono debe tener al menos ${PHONE_MIN_DIGITS} dígitos.`);
        if (!document.getElementById("regEmail").checkValidity()) return showRegisterError("Ingrese un correo electrónico válido.");
        if (password.length < PASSWORD_MIN) return showRegisterError(`La contraseña debe tener al menos ${PASSWORD_MIN} caracteres.`);
        if (password !== passwordConfirm) return showRegisterError("Las contraseñas no coinciden.");
        if (!terms.checked) return showRegisterError("Debe aceptar los términos y condiciones.");

        // Envío al servidor (los nombres de los campos son los de la BD / Laravel)
        btn.disabled = true;
        const res = await postJson(form.action, {
            ruc: val("regRuc"),
            dni: val("regDni"),
            names: val("regNames"),
            surnames: val("regSurnames"),
            email: val("regEmail"),
            phone: val("regPhone"),
            password: password,
            password_confirmation: passwordConfirm,
            terms: true,
        });

        if (!res.ok) {
            showRegisterError(serverMessage(res)); // ej. "Este correo ya está registrado."
            syncButton();
            return;
        }

        // Usuario creado y con sesión iniciada: se muestra el éxito y se entra al panel
        showRegisterError("");
        success.style.display = "block";
        setTimeout(() => { window.location.href = res.data.redirect; }, REDIRECT_DELAY_MS);
    });
}

function showRegisterError(msg) {
    const box = document.getElementById("registerError");
    if (!box) return;
    box.innerText = msg;
    box.style.display = msg ? "block" : "none";
}

// Deja solo números en un input y corta en el límite indicado
function bindDigits(inputId, max) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener("input", () => {
        input.value = input.value.replace(/\D/g, "").slice(0, max);
    });
}

/* 2. ANIMACIÓN DEL SELECTOR (TAB SLIDER) */
window.switchTab = function(type) {
    const slider = document.getElementById('pillSlider');
    const btnLogin = document.getElementById('btnTabLogin');
    const btnReg = document.getElementById('btnTabReg');
    const loginBlock = document.getElementById('loginBlock');
    const regBlock = document.getElementById('registerBlock');

    if (!slider || !btnLogin || !btnReg || !loginBlock || !regBlock) return;

    if (type === 'login') {
        slider.style.transform = 'translateX(0%)';
        btnLogin.classList.add('active');
        btnReg.classList.remove('active');
        loginBlock.classList.remove('d-none');
        regBlock.classList.add('d-none');
    } else {
        slider.style.transform = 'translateX(100%)';
        btnReg.classList.add('active');
        btnLogin.classList.remove('active');
        regBlock.classList.remove('d-none');
        loginBlock.classList.add('d-none');
    }
};

/* 3. VER/OCULTAR CONTRASEÑA */
window.togglePasswordVisibility = function(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const icon = input.nextElementSibling ? input.nextElementSibling.querySelector('i') : null;

    if (input.type === 'password') {
        input.type = 'text';
        if (icon) icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        if (icon) icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
};

/* 4. MODO OSCURO / CLARO */
function initTheme() {
    const btn = document.getElementById("btnThemeToggle");
    const icon = document.getElementById("themeIcon");
    const text = document.getElementById("themeText");
    const savedTheme = localStorage.getItem("theme");

    if (!btn) return;

    if (savedTheme === "dark") {
        document.documentElement.setAttribute("data-theme", "dark");
        if (icon) icon.className = "fa-solid fa-sun";
        if (text) text.innerText = "Claro";
    }

    btn.addEventListener("click", () => {
        const isDark = document.documentElement.getAttribute("data-theme") === "dark";
        if (isDark) {
            document.documentElement.removeAttribute("data-theme");
            localStorage.setItem("theme", "light");
            if (icon) icon.className = "fa-solid fa-moon";
            if (text) text.innerText = "Oscuro";
        } else {
            document.documentElement.setAttribute("data-theme", "dark");
            localStorage.setItem("theme", "dark");
            if (icon) icon.className = "fa-solid fa-sun";
            if (text) text.innerText = "Claro";
        }
    });
}