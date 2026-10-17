import { crearReserva } from './api.js';
import { mostrarAviso, ejecutar } from './aviso.js';

// Elementos de la página. Los textos los entrega la plantilla en los atributos data- de [data-textos].
const formulario = document.querySelector('[data-formulario]');
const botonCrear = formulario.querySelector('button[type="submit"]');
const textos = document.querySelector('[data-textos]').dataset;

// Pinta el error de cada campo bajo él. Los campos que no vengan en errores quedan limpios.
function mostrarErrores(errores) {
    for (const lugar of formulario.querySelectorAll('[data-error]')) {
        lugar.textContent = errores[lugar.dataset.error] || '';
    }
}

// Lee los campos del formulario quitando los espacios sobrantes.
function leerDatos() {
    const datos = {};

    for (const [campo, valor] of new FormData(formulario)) {
        datos[campo] = valor.trim();
    }

    return datos;
}

// Valida en el navegador y devuelve el error de cada campo incorrecto (vacío si todo está bien).
// El servidor vuelve a validar todo: esto solo evita viajes innecesarios.
function validar(datos) {
    const errores = {};

    const obligatorios = ['nombre_huesped', 'email_huesped', 'nombre_alojamiento', 'fecha_entrada', 'fecha_salida', 'importe'];
    for (const campo of obligatorios) {
        if (datos[campo] === '') {
            errores[campo] = textos.obligatorio;
        }
    }

    if (!errores.email_huesped && !/^\S+@\S+\.\S+$/.test(datos.email_huesped)) {
        errores.email_huesped = textos.emailNoValido;
    }

    // Con formato AAAA-MM-DD las fechas se pueden comparar como texto.
    if (!errores.fecha_entrada && !errores.fecha_salida && datos.fecha_salida <= datos.fecha_entrada) {
        errores.fecha_salida = textos.fechaSalidaPosterior;
    }

    if (!errores.importe && !(Number(datos.importe) >= 0)) {
        errores.importe = textos.importeNoValido;
    }

    return errores;
}

// Envía la reserva a la API con el botón desactivado para evitar envíos dobles.
// Si el servidor devuelve errores por campo, se pintan y el error sigue hacia arriba para mostrar el aviso.
async function enviar(datos) {
    botonCrear.disabled = true;

    try {
        await crearReserva(datos);
        formulario.reset();
    } catch (error) {
        mostrarErrores(error.campos || {});
        throw error;
    } finally {
        botonCrear.disabled = false;
    }
}

// Crear la reserva sin recargar la página.
formulario.addEventListener('submit', (evento) => {
    evento.preventDefault();

    const datos = leerDatos();
    const errores = validar(datos);

    mostrarAviso('', '');
    mostrarErrores(errores);

    if (Object.keys(errores).length > 0) {
        return;
    }

    // La API espera el importe como número, no como texto.
    datos.importe = Number(datos.importe);
    ejecutar(() => enviar(datos), textos.creacionOk);
});