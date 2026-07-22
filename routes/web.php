<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\ProductoController as PublicProductoController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\CategoriaProductoController;
use App\Http\Controllers\Admin\TipoIvaController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\CursoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ResenaController;
// ─── Landing ──────────────────────────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('inicio');

// ─── Tienda pública ───────────────────────────────────────────────────────────
Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda.index');
Route::get('/productos/{producto}', [PublicProductoController::class, 'show'])->name('producto.show');

// ── Reseñas de productos (usuarios logueados o invitados con nombre/correo) ───
Route::post('/productos/{producto}/resenas', [ResenaController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('resenas.store');

// ─── Invitados ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login',   [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/forgot-password',  [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendCode'])->name('password.sendCode');

    Route::get('/reset-password',   [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',  [ForgotPasswordController::class, 'reset'])->name('password.update');
});

// ─── Logout ───────────────────────────────────────────────────────────────────
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ─── Verificación de email ────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/verify-email',   [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/verify-email',  [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/verify-resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
});

// ─── Rutas protegidas ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'email.verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Solo Administrador ────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:Administrador')->group(function () {

        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');  // antes de {usuario}
        Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::post('/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleEstado'])->name('usuarios.toggle');

        Route::get('/roles',    [RolesController::class, 'index'])->name('roles');
        Route::post('/roles/{usuario}/update-role', [RolesController::class, 'updateRole'])->name('roles.update');

        Route::resource('categorias', CategoriaProductoController::class)->except(['show', 'destroy']);
        Route::post('categorias/{categoria}/toggle', [CategoriaProductoController::class, 'toggleEstado'])->name('categorias.toggle');

        Route::resource('tipo-iva', TipoIvaController::class)->except(['show', 'destroy']);
    });

    // ── Admin + Asesor + Gerente: catálogo e inventario ───────────────────────
    // Todos ven, Admin y Asesor pueden modificar (controlado en vistas/controladores)
    Route::prefix('admin')->name('admin.')->middleware('role:Administrador,Asesor,Gerente')->group(function () {
        Route::resource('productos', ProductoController::class)->except(['show', 'destroy']);
        Route::post('productos/{producto}/toggle', [ProductoController::class, 'toggleEstado'])->name('productos.toggle');

        Route::resource('cursos', CursoController::class)->except(['show', 'destroy']);
        Route::post('cursos/{curso}/toggle', [CursoController::class, 'toggleEstado'])->name('cursos.toggle');

        Route::get('/inventario', [InventarioController::class, 'index'])->name('inventario');
        Route::post('/inventario/{inventario}/actualizar', [InventarioController::class, 'actualizar'])->name('inventario.actualizar');
    });

    // ── Admin + Asesor + Gerente: ventas y reportes ───────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:Administrador,Asesor,Gerente')->group(function () {
        Route::get('/ventas',   fn() => view('admin.ventas'))->name('ventas');
        Route::get('/reportes', fn() => view('admin.reportes'))->name('reportes');
    });

    // ── Mayorista: informativo (Admin + Gerente) ──────────────────────────────
    Route::prefix('mayorista')->name('mayorista.')->middleware('role:Administrador,Gerente')->group(function () {
        Route::get('/', fn() => view('mayorista.index'))->name('index');
    });

    // ── Carrito (solo Clientes) ───────────────────────────────────────────────
    Route::prefix('carrito')->name('carrito.')->middleware('role:Cliente')->group(function () {
        Route::get('/',                      [CarritoController::class, 'index'])->name('index');
        Route::post('/agregar',              [CarritoController::class, 'agregar'])->name('agregar');
        Route::post('/actualizar/{detalle}', [CarritoController::class, 'actualizar'])->name('actualizar');
        Route::delete('/eliminar/{detalle}', [CarritoController::class, 'eliminar'])->name('eliminar');
        Route::post('/vaciar',               [CarritoController::class, 'vaciar'])->name('vaciar');
    });

    // ── Cliente ───────────────────────────────────────────────────────────────
    Route::prefix('cliente')->name('cliente.')->group(function () {
        Route::get('/pedidos', fn() => view('cliente.pedidos'))->name('pedidos');
        Route::get('/cursos',  fn() => view('cliente.cursos'))->name('cursos');
    });
});

Route::get('/send-test-mail', function () {
    Mail::to('sg0077010@gmail.com')->send(new TestMail());
    return response('Correo de prueba enviado a sg0077010@gmail.com', 200);
});
