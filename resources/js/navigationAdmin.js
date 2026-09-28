document.addEventListener("DOMContentLoaded", () => {
    initThemeToggle();
    initSidebarToggle();
});

/* Alerta para botones aún sin desarrollar */
window.devAlert = function (e) {
    e.preventDefault();
    alert("🚧 Sección en desarrollo");
};

/* Modo Oscuro / Claro */
function initThemeToggle() {
    const btn = document.getElementById("btnThemeToggle");
    const icon = document.getElementById("themeIcon");
    const text = document.getElementById("themeText");
    if (!btn) return;

    const isDark = document.documentElement.getAttribute("data-theme") === "dark";
    setThemeUI(isDark, icon, text);

    btn.addEventListener("click", () => {
        const nowDark = document.documentElement.getAttribute("data-theme") === "dark";
        if (nowDark) {
            document.documentElement.removeAttribute("data-theme");
            localStorage.setItem("theme", "light");
            setThemeUI(false, icon, text);
        } else {
            document.documentElement.setAttribute("data-theme", "dark");
            localStorage.setItem("theme", "dark");
            setThemeUI(true, icon, text);
        }
    });
}

function setThemeUI(isDark, icon, text) {
    if (icon) icon.className = isDark ? "fa-solid fa-sun" : "fa-solid fa-moon";
    if (text) text.innerText = isDark ? "Claro" : "Oscuro";
}

/* Colapsar / Expandir Sidebar */
function initSidebarToggle() {
    const btn = document.getElementById("btnToggleSidebar");
    if (!btn) return;

    btn.addEventListener("click", () => {
        const collapsed = document.documentElement.classList.toggle("sidebar-is-collapsed");
        localStorage.setItem("sidebar_collapsed", collapsed ? "true" : "false");
    });
}