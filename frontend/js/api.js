// Única capa de acceso a la API.

// Hace la petición y devuelve el JSON de la respuesta.
// Si la API responde con error, lanza un Error con el mensaje del servidor.
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
        throw new Error(datos.error.mensaje);
    }

    return datos;
}

// Lista las reservas. Recibe los filtros como URLSearchParams (estado, desde, hasta y q).
export function listarReservas(filtros) {
    return pedir('GET', '/api/reservas?' + filtros);
}

// Cambia el estado de una reserva (por ejemplo a 'cancelada').
export function cambiarEstado(id, estado) {
    return pedir('PATCH', '/api/reservas/' + id + '/estado', { estado: estado });
}