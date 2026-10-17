// Única capa de acceso a la API.

// Hace la petición y devuelve el JSON de la respuesta.
// Si la API responde con error, lanza un Error con el mensaje del servidor
// y, si trae errores por campo, en error.campos.
// Si no hay red o la respuesta no es JSON, lanza un Error sin mensaje.
async function pedir(metodo, url, cuerpo) {
    let respuesta;
    let datos;

    try {
        respuesta = await fetch(url, {
            method: metodo,
            headers: { 'Content-Type': 'application/json' },
            body: cuerpo ? JSON.stringify(cuerpo) : undefined,
        });
        datos = await respuesta.json();
    } catch (e) {
        throw new Error('');
    }

    if (!respuesta.ok) {
        const error = new Error(datos.error.mensaje);
        error.campos = datos.error.campos;
        throw error;
    }

    return datos;
}

// Lista las reservas. Recibe los filtros como URLSearchParams (estado, desde, hasta, q y alojamiento).
export function listarReservas(filtros) {
    return pedir('GET', '/api/reservas?' + filtros);
}

// Cambia el estado de una reserva (por ejemplo a 'cancelada').
export function cambiarEstado(id, estado) {
    return pedir('PATCH', '/api/reservas/' + id + '/estado', { estado: estado });
}

// Crea una reserva con los datos del formulario.
export function crearReserva(datos) {
    return pedir('POST', '/api/reservas', datos);
}