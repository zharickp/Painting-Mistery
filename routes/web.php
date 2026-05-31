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
use App\Http\Controllers\Admin\RolesController;

// ─── Landing ────────────────────────────────────────────────────────────────
Route::get('/', [LandingController::class, 'index'])->name('inicio');

// ─── Invitados ───────────────────────────────────────────────────────────────
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

// ─── Logout ──────────────────────────────────────────────────────────────────
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// ─── Verificación de email (auth requerido, sin verificar) ───────────────────
Route::middleware('auth')->group(function () {
    Route::get('/verify-email',   [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/verify-email',  [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/verify-resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
});

// ─── Rutas protegidas (auth + email verificado) ──────────────────────────────
Route::middleware(['auth', 'email.verified'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Admin ─────────────────────────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:Administrador,Asesor')->group(function () {
        Route::get('/usuarios',   fn() => view('admin.usuarios'))->name('usuarios');
        Route::get('/roles',      [RolesController::class, 'index'])->name('roles');
        Route::post('/roles/{usuario}/update-role', [RolesController::class, 'updateRole'])->name('roles.update');
        Route::get('/productos',  fn() => view('admin.productos'))->name('productos');
        Route::get('/inventario', fn() => view('admin.inventario'))->name('inventario');
        Route::get('/cursos',     fn() => view('admin.cursos'))->name('cursos');
        Route::get('/ventas',     fn() => view('admin.ventas'))->name('ventas');
        Route::get('/reportes',   fn() => view('admin.reportes'))->name('reportes');
    });

    // ── Mayorista (solo admin) ─────────────────────────────────────────────
    Route::prefix('mayorista')->name('mayorista.')->middleware('role:Administrador')->group(function () {
        Route::get('/', fn() => view('mayorista.index'))->name('index');
    });

    // ── Cliente ───────────────────────────────────────────────────────────────
    Route::prefix('cliente')->name('cliente.')->group(function () {
        Route::get('/pedidos', fn() => view('cliente.pedidos'))->name('pedidos');
        Route::get('/cursos',  fn() => view('cliente.cursos'))->name('cursos');
    });
});

// Ruta de prueba para enviar un correo de test
Route::get('/send-test-mail', function () {
    Mail::to('sg0077010@gmail.com')->send(new TestMail());
    return response('Correo de prueba enviado a sg0077010@gmail.com', 200);
});
