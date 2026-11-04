// Abre o cierra el menú en el teléfono.
const botonMenu = document.getElementById('abrir-menu');
const menu = document.getElementById('menu');

if (botonMenu && menu) {
    botonMenu.addEventListener('click', function () {
        const abierto = menu.classList.toggle('abierto');
        botonMenu.setAttribute('aria-expanded', abierto);
        botonMenu.setAttribute('aria-label', abierto ? 'Cerrar menú' : 'Abrir menú');
    });
}

// Pide confirmar antes de eliminar un registro.
document.querySelectorAll('[data-confirmar]').forEach(function (boton) {
    boton.addEventListener('click', function (evento) {
        if (!window.confirm(boton.dataset.confirmar)) {
            evento.preventDefault();
        }
    });
});
