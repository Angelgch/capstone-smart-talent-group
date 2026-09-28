document.addEventListener("DOMContentLoaded", () => {
    initTheme();
    initLanguage();
    initLoginForm();
});

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

        // REDIRECCIONES EXACTAS SEGÚN TU web.php
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

/* 5. TRADUCCIÓN (ES / EN) */
const translations = {
    es: {
        slogan: "CONECTA CON EL TALENTO HUMANO MEJOR CALIFICADO",
        tab_login: "Ingresar",
        tab_register: "Registrarse",
        login_title: "Iniciar sesión",
        login_sub: "Accede con tus credenciales de Smart Talent",
        email_label: "Correo electrónico",
        pwd_label: "Contraseña",
        login_btn: "Ingresar al sistema",
        demo_title: "Datos de Prueba:",
        reg_title: "Crear Cuenta",
        reg_sub: "Regístrate para solicitar evaluaciones corporativas",
        name_label: "Nombre completo",
        phone_label: "Teléfono",
        confirm_pwd: "Confirmar",
        reg_btn: "Registrarse",
        reg_success: "¡Registro Exitoso! Redirigiendo..."
    },
    en: {
        slogan: "CONNECT WITH THE BEST QUALIFIED HUMAN TALENT",
        tab_login: "Sign In",
        tab_register: "Register",
        login_title: "Sign In",
        login_sub: "Access with your Smart Talent credentials",
        email_label: "Email Address",
        pwd_label: "Password",
        login_btn: "Sign In to System",
        demo_title: "Test Credentials:",
        reg_title: "Create Account",
        reg_sub: "Register to request corporate assessments",
        name_label: "Full Name",
        phone_label: "Phone Number",
        confirm_pwd: "Confirm",
        reg_btn: "Register Now",
        reg_success: "Registration Successful! Redirecting..."
    }
};

function initLanguage() {
    const btn = document.getElementById("btnLangToggle");
    if (!btn) return;

    let currentLang = localStorage.getItem("lang") || "es";
    applyLanguage(currentLang);

    btn.addEventListener("click", () => {
        currentLang = currentLang === "es" ? "en" : "es";
        localStorage.setItem("lang", currentLang);
        applyLanguage(currentLang);
    });
}

function applyLanguage(lang) {
    const langText = document.getElementById("langText");
    if (langText) langText.innerText = lang.toUpperCase();
    
    document.querySelectorAll("[data-i18n]").forEach(el => {
        const key = el.getAttribute("data-i18n");
        if (translations[lang] && translations[lang][key]) {
            el.innerText = translations[lang][key];
        }
    });
}