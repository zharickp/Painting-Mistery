<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TipoDocumentoController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\Api\PermisoController;
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
use App\Http\Controllers\Api\RolesPermisosController;


Route::apiResource('tipo-documento', TipoDocumentoController::class);
Route::apiResource('usuarios', UsuarioController::class);
Route::apiResource('roles', RolController::class);
Route::apiResource('permisos', PermisoController::class);
Route::apiResource('tipo-iva', TipoIvaController::class);
Route::apiResource('categorias', CategoriaProductoController::class);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('inventario', InventarioController::class);
Route::apiResource('carrito', CarritoController::class);
Route::apiResource('carrito-detalle', CarritoDetalleController::class);
Route::apiResource('cursos', CursoController::class);
Route::apiResource('inscripciones', InscripcionController::class);
Route::apiResource('ventas', VentaController::class);
Route::apiResource('detalle-venta-producto', DetalleVentaProductoController::class);
Route::apiResource('detalle-venta-curso', DetalleVentaCursoController::class);
Route::apiResource('metodos-pago', MetodoPagoController::class);
Route::apiResource('pagos', PagoController::class);

Route::apiResource('usuarios-roles', UsuariosRolesController::class)
    ->only(['index', 'store', 'destroy']);

Route::apiResource('roles-permisos', RolesPermisosController::class)
    ->only(['index', 'store', 'destroy']);
