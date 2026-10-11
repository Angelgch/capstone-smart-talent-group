// resources/js/edit-user.js   (REEMPLAZA al anterior)
// Solo para el DETALLE editable del usuario (data-mode="edit"):
// al desmarcar un servicio ya REALIZADO, avisa de la posible penalización y deja arrepentirse.

const MSG_CANCEL_COMPLETED =
    '⚠️ Este servicio ya fue realizado.\n\n' +
    'Si lo cancelas puede aplicarse una penalización.\n\n' +
    '¿Deseas cancelarlo de todas formas?';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('requestForm');
    if (!form || form.dataset.mode !== 'edit') return; // en crear y otras páginas no hace nada

    form.querySelectorAll('.service-row[data-status="realizado"]').forEach((row) => {
        const checkbox = row.querySelector('.service-cb');
        if (!checkbox) return;

        // Fase de captura (true): corre ANTES que user-create.js. Si se arrepiente, ese código ni se entera.
        row.addEventListener('change', (e) => {
            if (e.target !== checkbox || checkbox.checked) return; // solo cuando lo desmarca

            if (!window.confirm(MSG_CANCEL_COMPLETED)) {
                e.stopPropagation();
                checkbox.checked = true; // se queda marcado
            }
        }, true);
    });
});