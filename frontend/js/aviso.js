// Avisos de carga, éxito y error compartidos por las páginas con JS.
// Cada página trae su <p data-aviso> y sus textos en los atributos data- de [data-textos].
const aviso = document.querySelector('[data-aviso]');
const textos = document.querySelector('[data-textos]').dataset;

// Muestra un aviso de tipo cargando, exito o error. Con texto vacío lo oculta.
export function mostrarAviso(texto, tipo) {
    aviso.textContent = texto;
    aviso.className = 'aviso aviso-' + tipo;
    aviso.hidden = texto === '';
}

// Muestra "cargando" mientras dura la acción y después el aviso de éxito o de error si falla.
// Si el error no trae mensaje (sin red), se usa el texto de error de conexión.
export async function ejecutar(accion, textoExito) {
    mostrarAviso(textos.cargando, 'cargando');

    try {
        await accion();
        mostrarAviso(textoExito, 'exito');
    } catch (error) {
        mostrarAviso(error.message || textos.errorRed, 'error');
    }
}