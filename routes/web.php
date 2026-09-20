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
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ProductoController;
use App\Http\Controllers\Admin\CursoController;
use App\Http\Controllers\Admin\InventarioController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\AuditoriaController;
use App\Http\Controllers\Admin\RespaldoController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
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

        Route::resource('banners', BannerController::class)->except(['show']);
        Route::post('banners/{banner}/toggle', [BannerController::class, 'toggleEstado'])->name('banners.toggle');

        // Copias de seguridad — solo Administrador
        Route::get('/respaldos',            [RespaldoController::class, 'index'])->name('respaldos.index');
        Route::post('/respaldos',           [RespaldoController::class, 'store'])->name('respaldos.store');
        Route::get('/respaldos/{nombre}',   [RespaldoController::class, 'download'])->name('respaldos.download');
        Route::delete('/respaldos/{nombre}',[RespaldoController::class, 'destroy'])->name('respaldos.destroy');
    });

    // ── Admin + Gerente + Asesor: AUDITORÍA (solo consulta) ───────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:Administrador,Gerente')->group(function () {
        Route::get('/auditoria',              [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/{auditoria}',  [AuditoriaController::class, 'show'])->name('auditoria.show');
    });

    // ── Escritura de catálogo e inventario: SOLO Administrador y Asesor ───────
    Route::prefix('admin')->name('admin.')->middleware('role:Administrador,Asesor')->group(function () {
        Route::get('productos/create',            [ProductoController::class, 'create'])->name('productos.create');
        Route::post('productos',                  [ProductoController::class, 'store'])->name('productos.store');
        Route::get('productos/{producto}/edit',   [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('productos/{producto}',        [ProductoController::class, 'update'])->name('productos.update');
        Route::patch('productos/{producto}',      [ProductoController::class, 'update']);
        Route::post('productos/{producto}/toggle',[ProductoController::class, 'toggleEstado'])->name('productos.toggle');

        Route::get('cursos/create',            [CursoController::class, 'create'])->name('cursos.create');
        Route::post('cursos',                  [CursoController::class, 'store'])->name('cursos.store');
        Route::get('cursos/{curso}/edit',      [CursoController::class, 'edit'])->name('cursos.edit');
        Route::put('cursos/{curso}',           [CursoController::class, 'update'])->name('cursos.update');
        Route::patch('cursos/{curso}',         [CursoController::class, 'update']);
        Route::post('cursos/{curso}/toggle',   [CursoController::class, 'toggleEstado'])->name('cursos.toggle');

        Route::post('/inventario/{inventario}/actualizar', [InventarioController::class, 'actualizar'])->name('inventario.actualizar');
    });

    // ── Consulta de catálogo, inventario, ventas y reportes (Admin + Asesor + Gerente) ─
    Route::prefix('admin')->name('admin.')->middleware('role:Administrador,Asesor,Gerente')->group(function () {
        Route::get('productos',              [ProductoController::class, 'index'])->name('productos.index');
        Route::get('cursos',                 [CursoController::class, 'index'])->name('cursos.index');
        Route::get('/inventario',            [InventarioController::class, 'index'])->name('inventario');
        Route::get('/ventas',                fn() => view('admin.ventas'))->name('ventas');
        Route::get('/reportes',              fn() => view('admin.reportes'))->name('reportes');
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

    // ── Checkout (pago SIMULADO — solo Clientes) ─────────────────────────────
    Route::middleware('role:Cliente')->group(function () {
        Route::get('/checkout',   [CheckoutController::class, 'mostrar'])->name('checkout.mostrar');
        Route::post('/checkout',  [CheckoutController::class, 'procesar'])->name('checkout.procesar');
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
