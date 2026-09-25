@php
    $nombreRuta = request()->route()?->getName() ?? '';
    $partes = explode('.', $nombreRuta);

    $modulos = [
        'productos'     => ['Productos',          'admin.productos.index'],
        'categorias'    => ['Categorías',         'admin.categorias.index'],
        'tipo-iva'      => ['Tipos de IVA',       'admin.tipo-iva.index'],
        'cursos'        => ['Cursos',             'admin.cursos.index'],
        'inventario'    => ['Inventario',         'admin.inventario'],
        'ventas'        => ['Ventas',             'admin.ventas'],
        'reportes'      => ['Reportes',           'admin.reportes'],
        'usuarios'      => ['Usuarios',           'admin.usuarios.index'],
        'roles'         => ['Roles',              'admin.roles'],
        'auditoria'     => ['Auditoría',          'admin.auditoria.index'],
        'banners'       => ['Banners',            'admin.banners.index'],
        'respaldos'     => ['Copias de seguridad', 'admin.respaldos.index'],
        'tarifas-envio' => ['Tarifas de envío',   'admin.tarifas-envio.index'],
        'resenas-sitio' => ['Reseñas',            'admin.resenas-sitio.index'],
        'agenda-cursos' => ['Agenda de cursos',   'admin.agenda-cursos.index'],
    ];
    $acciones = ['create' => 'Nuevo', 'edit' => 'Editar', 'show' => 'Detalle', 'inscripciones' => 'Inscripciones', 'orden' => 'Orden de venta'];

    $migas = [];
    if (($partes[0] ?? '') === 'admin' && isset($modulos[$partes[1] ?? ''])) {
        [$etiqueta, $rutaIndice] = $modulos[$partes[1]];
        $accion = $acciones[$partes[2] ?? ''] ?? null;
        $migas[] = [$etiqueta, $accion ? $rutaIndice : null];
        if ($accion) { $migas[] = [$accion, null]; }
    } elseif (($partes[0] ?? '') === 'mayorista') {
        $migas[] = ['Mayoristas', null];
    }
@endphp
@if($migas)
<nav aria-label="Ruta de navegación" class="mb-4 flex flex-wrap items-center gap-1.5 text-xs text-slate-400">
    <a href="{{ route('dashboard') }}" class="hover:text-red-600 transition">Inicio</a>
    @foreach($migas as [$texto, $ruta])
        <span aria-hidden="true">/</span>
        @if($ruta)
            <a href="{{ route($ruta) }}" class="hover:text-red-600 transition">{{ $texto }}</a>
        @else
            <span class="font-semibold text-slate-600">{{ $texto }}</span>
        @endif
    @endforeach
</nav>
@endif
