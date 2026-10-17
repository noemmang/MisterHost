import { listarReservas, cambiarEstado } from './api.js';
import { ejecutar } from './aviso.js';

// Elementos de la página. Los textos los entrega la plantilla en los atributos data- de [data-textos].
const formulario = document.querySelector('[data-filtros]');
const contenedorTabla = document.querySelector('[data-tabla]');
const cuerpoTabla = contenedorTabla.querySelector('tbody');
const plantillaFila = document.querySelector('[data-plantilla-fila]');
const seleccionarTodas = document.querySelector('[data-seleccionar-todas]');
const botonCancelar = document.querySelector('[data-accion="cancelar"]');
const sinResultados = document.querySelector('[data-vacio]');
const textos = document.querySelector('[data-textos]').dataset;

// Pasa una fecha AAAA-MM-DD a DD/MM/AAAA, igual que en la plantilla Twig.
function formatearFecha(fecha) {
    return fecha.split('-').reverse().join('/');
}

// Da formato al importe como en Twig: 1.234,50 €
function formatearImporte(importe) {
    const numero = importe.toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
        useGrouping: 'always',
    });

    return numero + ' ' + textos.moneda;
}

// Crea la fila de una reserva copiando la plantilla. Los datos se insertan con textContent.
function crearFila(reserva) {
    const fila = plantillaFila.content.firstElementChild.cloneNode(true);

    const valores = {
        id: reserva.id,
        nombre_huesped: reserva.nombre_huesped,
        nombre_alojamiento: reserva.nombre_alojamiento,
        fecha_entrada: formatearFecha(reserva.fecha_entrada),
        fecha_salida: formatearFecha(reserva.fecha_salida),
        estado: textos[reserva.estado],
        importe: formatearImporte(reserva.importe),
    };

    for (const campo in valores) {
        fila.querySelector('[data-campo="' + campo + '"]').textContent = valores[campo];
    }

    fila.dataset.id = reserva.id;
    fila.querySelector('[data-campo="estado"]').classList.add('estado-' + reserva.estado);

    // Una reserva cancelada ya no se puede cancelar, así que no se puede marcar.
    const casilla = fila.querySelector('[data-seleccion]');
    casilla.value = reserva.id;
    casilla.disabled = reserva.estado === 'cancelada';

    return fila;
}

// Activa el botón de cancelar solo si hay filas marcadas y deja la casilla de "todas" acorde a las filas.
function actualizarSeleccion() {
    const posibles = cuerpoTabla.querySelectorAll('[data-seleccion]:not(:disabled)');
    const marcadas = cuerpoTabla.querySelectorAll('[data-seleccion]:checked');

    botonCancelar.disabled = marcadas.length === 0;
    seleccionarTodas.checked = posibles.length > 0 && marcadas.length === posibles.length;
}

// Repinta la tabla con las reservas recibidas. Si no hay ninguna, muestra el mensaje de vacío.
function repintarTabla(reservas) {
    cuerpoTabla.replaceChildren();

    for (const reserva of reservas) {
        cuerpoTabla.append(crearFila(reserva));
    }

    contenedorTabla.hidden = reservas.length === 0;
    sinResultados.hidden = reservas.length > 0;
    actualizarSeleccion();
}

// Pide las reservas con los filtros del formulario y repinta la tabla.
async function cargarListado() {
    const filtros = new URLSearchParams(new FormData(formulario));

    repintarTabla(await listarReservas(filtros));
}

// Cancela las reservas marcadas tras pedir confirmación. Se cancelan una a una.
// La tabla se recarga siempre, aunque alguna falle, para que muestre lo que sí se ha cancelado.
function cancelar() {
    if (!confirm(textos.confirmar)) {
        return;
    }

    const ids = [];
    for (const casilla of cuerpoTabla.querySelectorAll('[data-seleccion]:checked')) {
        ids.push(casilla.value);
    }

    ejecutar(async () => {
        try {
            for (const id of ids) {
                await cambiarEstado(id, 'cancelada');
            }
        } finally {
            await cargarListado();
        }
    }, textos.cancelacionOk);
}

// Filtrar sin recargar la página.
formulario.addEventListener('submit', (evento) => {
    evento.preventDefault();
    ejecutar(cargarListado, '');
});

// Las filas se crean al repintar, así que se escucha el cambio en el cuerpo de la tabla entero.
cuerpoTabla.addEventListener('change', actualizarSeleccion);

// Pulsar una fila abre su detalle, salvo en la celda de la casilla, que solo sirve para marcarla.
cuerpoTabla.addEventListener('click', (evento) => {
    if (evento.target.closest('[data-celda-seleccion]')) {
        return;
    }

    const fila = evento.target.closest('tr');
    window.location.href = '/reservas/' + fila.dataset.id;
});

// Marca o desmarca todas las filas que se pueden cancelar.
seleccionarTodas.addEventListener('change', () => {
    for (const casilla of cuerpoTabla.querySelectorAll('[data-seleccion]:not(:disabled)')) {
        casilla.checked = seleccionarTodas.checked;
    }

    actualizarSeleccion();
});

botonCancelar.addEventListener('click', cancelar);