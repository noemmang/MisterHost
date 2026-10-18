import { cambiarEstado } from './api.js';
import { ejecutar } from './aviso.js';

// Elementos de la página. Los textos los entrega la plantilla en los atributos data- de [data-textos].
const botonCancelar = document.querySelector('[data-accion="cancelar"]');
const estado = document.querySelector('[data-estado]');
const historial = document.querySelector('[data-historial]');
const textos = document.querySelector('[data-textos]').dataset;

// Pasa una fecha AAAA-MM-DD HH:MM:SS a DD/MM/AAAA HH:MM, igual que en la plantilla Twig.
function formatearFechaHora(fechaHora) {
    const [fecha, hora] = fechaHora.split(' ');

    return fecha.split('-').reverse().join('/') + ' ' + hora.slice(0, 5);
}

// Añade al final del historial el evento recibido, con la misma estructura que pinta Twig.
function anadirEvento(evento) {
    const fecha = document.createElement('time');
    fecha.dateTime = evento.fecha_creacion;
    fecha.textContent = formatearFechaHora(evento.fecha_creacion);

    const fila = document.createElement('li');
    fila.append(fecha, evento.descripcion);
    historial.append(fila);
}

// Deja la página como una reserva cancelada: nueva etiqueta de estado, sin botón y con el evento nuevo en el historial.
function mostrarCancelada(reserva) {
    estado.textContent = textos.cancelada;
    estado.className = 'estado estado-cancelada';
    botonCancelar.hidden = true;

    // Los eventos llegan ordenados por fecha, así que el último es el de la cancelación.
    anadirEvento(reserva.eventos[reserva.eventos.length - 1]);
}

// Cancela la reserva tras pedir confirmación, con el botón desactivado para evitar envíos dobles.
function cancelar() {
    if (!confirm(textos.confirmar)) {
        return;
    }

    ejecutar(async () => {
        botonCancelar.disabled = true;

        try {
            mostrarCancelada(await cambiarEstado(botonCancelar.dataset.id, 'cancelada'));
        } finally {
            botonCancelar.disabled = false;
        }
    }, textos.cancelacionOk);
}

botonCancelar.addEventListener('click', cancelar);