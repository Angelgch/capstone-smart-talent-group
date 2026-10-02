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

/* 1. MANEJO DEL LOGIN Y REDIRECCIÓN POR ROL */

function initLoginForm() {
    const loginForm = document.getElementById("loginForm");
    const loginError = document.getElementById("loginError");

    if (!loginForm) return;

    loginForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const email = document.getElementById("loginEmail").value.trim().toLowerCase();
        const password = document.getElementById("loginPassword").value.trim();

        if (loginError) {
            loginError.style.display = "none";
            loginError.innerText = "";
        }

        // TODO (BD): reemplazar por POST real a Laravel (Auth) y redirigir según users.role
        if (email === "admin@gmail.com" && password === "12345") {
            window.location.href = "/admin/dashboard";
        } else if (email === "user@gmail.com" && password === "12345") {
            window.location.href = "/user/dashboard";
        } else {
            if (loginError) {
                loginError.innerText = "Credenciales incorrectas. Verifique correo y contraseña.";
                loginError.style.display = "block";
            }
        }
    });
}

/* ==========================================================================
   1.b REGISTRO (BORRADOR: valida en pantalla; el guardado real llega con la BD)
   ========================================================================== */
function initRegisterForm() {
    const form = document.getElementById("registerForm");
    if (!form) return;

    const error = document.getElementById("registerError");
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

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        success.style.display = "none";

        const val = (id) => document.getElementById(id).value.trim();

        // Todos los campos son obligatorios
        const required = ["regRuc", "regDni", "regNames", "regSurnames", "regEmail", "regPhone", "regPassword", "regPasswordConfirm"];
        if (required.some((id) => !val(id))) return showRegisterError("Todos los campos son obligatorios.");

        if (val("regRuc").length !== RUC_DIGITS) return showRegisterError(`El RUC debe tener exactamente ${RUC_DIGITS} dígitos.`);
        if (val("regDni").length !== DNI_DIGITS) return showRegisterError(`El DNI debe tener exactamente ${DNI_DIGITS} dígitos.`);
        if (val("regPhone").length < PHONE_MIN_DIGITS) return showRegisterError(`El teléfono debe tener al menos ${PHONE_MIN_DIGITS} dígitos.`);
        if (!document.getElementById("regEmail").checkValidity()) return showRegisterError("Ingrese un correo electrónico válido.");
        if (val("regPassword").length < PASSWORD_MIN) return showRegisterError(`La contraseña debe tener al menos ${PASSWORD_MIN} caracteres.`);
        if (val("regPassword") !== val("regPasswordConfirm")) return showRegisterError("Las contraseñas no coinciden.");
        if (!terms.checked) return showRegisterError("Debe aceptar los términos y condiciones.");

        // TODO (paso siguiente): aquí va el POST a Laravel con: ruc, dni, names, surnames, email, phone,
        // password, password_confirmation y terms (se guarda users.terms_accepted_at).
        showRegisterError("");
        success.style.display = "block";
        form.reset();
        syncButton();
        setTimeout(() => window.switchTab("login"), 1800);
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
