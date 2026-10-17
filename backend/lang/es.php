<?php

declare(strict_types=1);

// Textos de la app en español. Cada clave se pide con Translator.
return [
    'error.no_encontrado' => 'El recurso solicitado no existe',
    'error.metodo_no_permitido' => 'Método no permitido',
    'error.filtro_no_valido' => 'Los filtros de búsqueda no son válidos',
    'error.json_no_valido' => 'El cuerpo de la petición no es un JSON válido',
    'error.validacion' => 'Datos no válidos',
    'error.transicion_no_valida' => 'No se puede cambiar la reserva a ese estado',
    'error.interno' => 'Ha ocurrido un error inesperado',

    'validacion.obligatorio' => 'Campo obligatorio',
    'validacion.demasiado_largo' => 'Texto demasiado largo',
    'validacion.email_no_valido' => 'Email no válido',
    'validacion.fecha_no_valida' => 'Fecha no válida (formato AAAA-MM-DD)',
    'validacion.fecha_salida_posterior' => 'La fecha de salida debe ser posterior a la de entrada',
    'validacion.importe_no_valido' => 'Importe no válido',
    'validacion.notas_no_validas' => 'Notas no válidas (máximo 1000 caracteres)',
    'validacion.estado_no_valido' => 'Estado no válido',

    'app.nombre' => 'MisterHost',
    'nav.volver_listado' => 'Volver al listado',

    'listado.titulo' => 'Reservas',
    'listado.vacio' => 'No hay reservas que coincidan con los filtros',
    'listado.acciones' => 'Acciones',
    'listado.ver' => 'Ver detalle',

    'filtro.estado' => 'Estado',
    'filtro.todos' => 'Todos',
    'filtro.desde' => 'Entrada desde',
    'filtro.hasta' => 'Entrada hasta',
    'filtro.huesped' => 'Huésped',
    'filtro.filtrar' => 'Filtrar',
    'filtro.limpiar' => 'Limpiar',

    'detalle.titulo' => 'Reserva',
    'detalle.historial' => 'Historial',

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

    'evento.creada' => 'Reserva creada en estado pendiente',
    'evento.confirmada' => 'Reserva confirmada',
    'evento.cancelada' => 'Reserva cancelada',
];