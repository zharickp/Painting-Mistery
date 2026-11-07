<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TipoDocumentoController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\TipoIvaController;
use App\Http\Controllers\Api\CategoriaProductoController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\InventarioController;
use App\Http\Controllers\Api\CarritoController;
use App\Http\Controllers\Api\CarritoDetalleController;
use App\Http\Controllers\Api\CursoController;
use App\Http\Controllers\Api\InscripcionController;
use App\Http\Controllers\Api\VentaController;
use App\Http\Controllers\Api\DetalleVentaProductoController;
use App\Http\Controllers\Api\DetalleVentaCursoController;
use App\Http\Controllers\Api\MetodoPagoController;
use App\Http\Controllers\Api\PagoController;
use App\Http\Controllers\Api\UsuariosRolesController;


// Toda la API administrativa exige sesión iniciada y rol Administrador.
// Los nombres llevan "api." adelante para no chocar con los de las páginas
// (por ejemplo carrito.index); si chocan, Laravel no puede guardar las rutas en caché.
Route::middleware(['web', 'auth', 'role:Administrador'])->name('api.')->group(function () {
    Route::apiResource('tipo-documento', TipoDocumentoController::class);
    Route::apiResource('usuarios', UsuarioController::class);
    Route::apiResource('roles', RolController::class);
    Route::apiResource('tipo-iva', TipoIvaController::class);
    Route::apiResource('categorias', CategoriaProductoController::class);
    Route::apiResource('productos', ProductoController::class);
    // Ventas, pagos, sus detalles y el inventario solo se consultan por la API:
    // se modifican desde el sistema, que aplica las reglas (stock, estados, pagos).
    Route::apiResource('inventario', InventarioController::class)->only(['index', 'show']);
    Route::apiResource('carrito', CarritoController::class);
    Route::apiResource('carrito-detalle', CarritoDetalleController::class);
    Route::apiResource('cursos', CursoController::class);
    Route::apiResource('inscripciones', InscripcionController::class);
    Route::apiResource('ventas', VentaController::class)->only(['index', 'show']);
    Route::apiResource('detalle-venta-producto', DetalleVentaProductoController::class)->only(['index', 'show']);
    Route::apiResource('detalle-venta-curso', DetalleVentaCursoController::class)->only(['index', 'show']);
    Route::apiResource('metodos-pago', MetodoPagoController::class);
    Route::apiResource('pagos', PagoController::class)->only(['index', 'show']);

    Route::apiResource('usuarios-roles', UsuariosRolesController::class)
        ->only(['index', 'store', 'destroy']);
});
