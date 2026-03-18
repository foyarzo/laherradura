<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\Admin\AdminHomeController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminAdvertenciaController;
use App\Http\Controllers\Admin\PuntoEncuentroController;

use App\Http\Controllers\Operador\OperadorHomeController;

use App\Http\Controllers\PedidoController;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\CartController;

use App\Http\Controllers\AIController;
use App\Http\Controllers\SettingController;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {

    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');

    Route::get('/recuperar-contrasena', [ForgotPasswordController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/recuperar-contrasena', [ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
        ->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/home', [LoginController::class, 'home'])->name('home');

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');

    Route::resource('pedidos', PedidoController::class)->except(['index', 'show']);
});

Route::get('/comprobantes/{file}', function ($file) {

    $file = basename((string) $file);
    $path = "comprobantes/{$file}";

    abort_unless(Storage::disk('public')->exists($path), 404);

    return Storage::disk('public')->response($path);

})->middleware('auth')->name('comprobantes.ver');

Route::middleware(['auth','role:admin'])->group(function () {

    Route::get('/admin/pedidos-dashboard', [PedidoController::class, 'dashboard'])
        ->name('pedidos.dashboard');

    Route::get('/admin/pedidos-export', [PedidoController::class, 'export'])
        ->name('pedidos.export');
});

Route::prefix('admin')
->name('admin.')
->middleware(['auth','role:admin'])
->group(function () {

    Route::get('/', [AdminHomeController::class, 'index'])->name('home');

    Route::get('/mensaje', [SettingController::class, 'editBienvenida'])->name('mensaje');
    Route::post('/mensaje', [SettingController::class, 'updateBienvenida'])->name('mensaje.update');

    Route::post('/ai/generar-descripcion', [AIController::class, 'generarDescripcion'])
        ->name('ai.generar.descripcion');

    Route::post('/ai/generar-imagen', [AIController::class, 'generarImagen'])
        ->name('ai.generar.imagen');

    Route::get('/ai/img/{path}', [AIController::class, 'serveAiImage'])
        ->where('path', '.*')
        ->name('ai.img');

    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('/usuarios', [AdminUserController::class, 'index'])->name('usuarios');
    Route::post('/usuarios', [AdminUserController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{user}', [AdminUserController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{user}', [AdminUserController::class, 'destroy'])->name('usuarios.destroy');

    Route::post('/advertencias', [AdminAdvertenciaController::class, 'store'])->name('advertencias.store');
    Route::delete('/advertencias/{advertencia}', [AdminAdvertenciaController::class, 'destroy'])->name('advertencias.destroy');

    Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda');

    Route::get('/productos', [AdminProductController::class, 'index'])->name('productos.index');

    Route::get('/productos-redirect', fn () => redirect()->route('admin.productos.index'))
        ->name('productos');

    Route::get('/productos/create', [AdminProductController::class, 'create'])->name('productos.create');
    Route::post('/productos', [AdminProductController::class, 'store'])->name('productos.store');
    Route::get('/productos/{product}/edit', [AdminProductController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{product}', [AdminProductController::class, 'update'])->name('productos.update');
    Route::delete('/productos/{product}', [AdminProductController::class, 'destroy'])->name('productos.destroy');

    Route::get('/puntos-encuentro', [PuntoEncuentroController::class, 'index'])->name('puntos.index');
    Route::post('/puntos-encuentro', [PuntoEncuentroController::class, 'store'])->name('puntos.store');
    Route::put('/puntos-encuentro/{punto}', [PuntoEncuentroController::class, 'update'])->name('puntos.update');
    Route::patch('/puntos-encuentro/{punto}/toggle', [PuntoEncuentroController::class, 'toggle'])->name('puntos.toggle');
    Route::delete('/puntos-encuentro/{punto}', [PuntoEncuentroController::class, 'destroy'])->name('puntos.destroy');

    Route::get('/pedidos', [PedidoController::class, 'admin'])->name('pedidos.admin');
    Route::patch('/pedidos/{pedido}/aprobar', [PedidoController::class, 'aprobar'])->name('pedidos.aprobar');
    Route::patch('/pedidos/{pedido}/rechazar', [PedidoController::class, 'rechazar'])->name('pedidos.rechazar');
});

Route::prefix('operador')
->name('operador.')
->middleware(['auth','role:operador'])
->group(function () {

    Route::get('/', [OperadorHomeController::class, 'index'])->name('home');

});

Route::prefix('tienda')
->name('tienda.')
->middleware(['auth'])
->group(function () {

    Route::get('/', [TiendaController::class, 'index'])->name('home');

    Route::get('/carrito', [CartController::class, 'index'])->name('carrito');

    Route::post('/carrito/add/{product}', [CartController::class, 'add'])->name('carrito.add');

    Route::post('/carrito/update', [CartController::class, 'update'])->name('carrito.update');

    Route::post('/carrito/remove/{product}', [CartController::class, 'remove'])->name('carrito.remove');

    Route::post('/carrito/clear', [CartController::class, 'clear'])->name('carrito.clear');

    Route::post('/carrito/checkout', [CartController::class, 'checkout'])->name('carrito.checkout');

});