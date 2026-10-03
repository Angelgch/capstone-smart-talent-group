// resources/js/user-edit.js
// Lógica exclusiva de la pantalla EDITAR solicitud.
//
// Reutiliza todo lo de user-create.js (Sí/No de documentos, teléfono, DNI), porque el
// formulario es el mismo. Aquí solo va lo que cambia al editar:
//   1) Cancelar un servicio ya COMPLETADO muestra una advertencia de penalización.
//
// Solo se activa si el formulario tiene data-mode="edit" (ver _form.blade.php).

/* ==========================================================================
   CONFIG
   ========================================================================== */
const MSG_CANCEL_COMPLETED =
    '⚠️ Este servicio ya fue completado.\n\n' +
    'Si lo cancelas puede aplicarse una penalización.\n\n' +
    '¿Deseas cancelarlo de todas formas?';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('requestForm');
    if (!form || form.dataset.mode !== 'edit') return; // en create y otras páginas no hace nada

    initCompletedCancelWarning(form);
});

/* ==========================================================================
   1. ADVERTENCIA AL CANCELAR UN SERVICIO COMPLETADO
   ========================================================================== */
function initCompletedCancelWarning(form) {
    // Solo filas cuyo estado es "Realizado" (completado)
    form.querySelectorAll('.service-row[data-status="Realizado"]').forEach((row) => {
        const checkbox = row.querySelector('.service-cb');
        if (!checkbox) return;

        // Fase de captura (true) sobre la fila: corre ANTES que el listener del checkbox
        // en user-create.js. Así, si el usuario se arrepiente, ese código ni se entera.
        row.addEventListener('change', (e) => {
            if (e.target !== checkbox) return;
            if (checkbox.checked) return; // volvió a marcarlo: no hay nada que advertir

            // De momento es un aviso simple (confirm). Aceptar = cancelar el servicio.
            const acepta = window.confirm(MSG_CANCEL_COMPLETED);
            if (!acepta) {
                e.stopPropagation();    // user-create.js no llega a resetear el Sí/No
                checkbox.checked = true; // se queda marcado
            }
        }, true);
    });
}