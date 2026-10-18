<?php

declare(strict_types=1);

// Textos de la app en español. Cada clave se pide con Translator.
return [
    // ---------- Errores de la API ----------
    'error.no_encontrado' => 'El recurso solicitado no existe',
    'error.metodo_no_permitido' => 'Método no permitido',
    'error.filtro_no_valido' => 'Los filtros de búsqueda no son válidos',
    'error.json_no_valido' => 'El cuerpo de la petición no es un JSON válido',
    'error.validacion' => 'Datos no válidos',
    'error.transicion_no_valida' => 'No se puede cambiar la reserva a ese estado',
    'error.interno' => 'Ha ocurrido un error inesperado',

    // ---------- Errores de validación de cada campo ----------
    'validacion.obligatorio' => 'Campo obligatorio',
    'validacion.demasiado_largo' => 'Texto demasiado largo',
    'validacion.email_no_valido' => 'Email no válido',
    'validacion.fecha_no_valida' => 'Fecha no válida (formato AAAA-MM-DD)',
    'validacion.fecha_salida_posterior' => 'La fecha de salida debe ser posterior a la de entrada',
    'validacion.importe_no_valido' => 'Importe no válido',
    'validacion.notas_no_validas' => 'Notas no válidas (máximo 1000 caracteres)',
    'validacion.estado_no_valido' => 'Estado no válido',

    // ---------- General ----------
    'app.nombre' => 'MisterHost',
    'nav.volver' => 'Volver',

    // ---------- Avisos y confirmaciones ----------
    'mensaje.cargando' => 'Cargando...',
    'mensaje.error_red' => 'No se ha podido conectar con el servidor',
    'mensaje.confirmar_cancelar' => '¿Cancelar las reservas seleccionadas?',
    'mensaje.cancelacion_ok' => 'Cancelación realizada correctamente',
    'mensaje.creacion_ok' => 'Reserva creada correctamente',

    // ---------- Página del listado ----------
    'listado.titulo' => 'Reservas',
    'listado.vacio' => 'No hay reservas que coincidan con los filtros',
    'listado.ver' => 'Ver detalle',
    'listado.seleccionar' => 'Seleccionar reserva',
    'listado.seleccionar_todas' => 'Seleccionar todas',
    'listado.cancelar' => 'Cancelar seleccionadas',
    'listado.nueva' => 'Nueva reserva',

    // ---------- Filtros del listado ----------
    'filtro.titulo' => 'Filtros',
    'filtro.estado' => 'Estado',
    'filtro.todos' => 'Todos',
    'filtro.desde' => 'Entrada desde',
    'filtro.hasta' => 'Entrada hasta',
    'filtro.huesped' => 'Huésped',
    'filtro.alojamiento' => 'Alojamiento',
    'filtro.filtrar' => 'Filtrar',
    'filtro.limpiar' => 'Limpiar',

    // ---------- Página del formulario de nueva reserva ----------
    'formulario.titulo' => 'Nueva reserva',
    'formulario.crear' => 'Crear reserva',

    // ---------- Página del detalle ----------
    'detalle.titulo' => 'Detalle',
    'detalle.resumen' => 'Resumen',
    'detalle.historial' => 'Historial',
    'detalle.cancelar' => 'Cancelar reserva',
    'detalle.confirmar_cancelar' => '¿Cancelar esta reserva?',

    // ---------- Datos de la reserva ----------
    'reserva.id' => 'Nº',
    'reserva.nombre_huesped' => 'Huésped',
    'reserva.email_huesped' => 'Email',
    'reserva.nombre_alojamiento' => 'Alojamiento',
    'reserva.fecha_entrada' => 'Entrada',
    'reserva.fecha_salida' => 'Salida',
    'reserva.estado' => 'Estado',
    'reserva.importe' => 'Importe',
    'reserva.moneda' => '€',
    'reserva.notas' => 'Notas',
    'reserva.fecha_creacion' => 'Creada el',
    'reserva.estado.pendiente' => 'Pendiente',
    'reserva.estado.confirmada' => 'Confirmada',
    'reserva.estado.cancelada' => 'Cancelada',

    // ---------- Descripción de los eventos del historial ----------
    'evento.creada' => 'Reserva creada en estado pendiente',
    'evento.confirmada' => 'Reserva confirmada',
    'evento.cancelada' => 'Reserva cancelada',
];