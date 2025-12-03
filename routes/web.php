<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\CheckUserActive;


Route::get('/', function () {
    return redirect('login');
});

Route::get('/dashboard', function () {
    return view('admin.index');
})->middleware(['auth', 'verified', 'active'])->name('dashboard');

//  , 'can:admin.unidades'
Route::prefix('admin/unidades')->middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/', [UnidadController::class, 'index'])->name('admin.unidades.index');
    // ->middleware('can:admin.unidades.create')
    Route::get('/crear', [UnidadController::class, 'create'])
        ->name('admin.unidades.create');

    // ->middleware('can:admin.unidades.create')
    Route::post('/', [UnidadController::class, 'store'])
        ->name('admin.unidades.store');

    // ->middleware('can:admin.unidades.edit')
    Route::get('/{unidad}/editar', [UnidadController::class, 'edit'])
        ->name('admin.unidades.edit');

    // ->middleware('can:admin.unidades.edit')
    Route::put('/{unidad}', [UnidadController::class, 'update'])
        ->name('admin.unidades.update');

    // ->middleware('can:admin.unidades.destroy')
    Route::delete('/{unidad}', [UnidadController::class, 'destroy'])
        ->name('admin.unidades.destroy');

    Route::get('/data', [UnidadController::class, 'data'])
        ->name('admin.unidades.data');
    Route::get('/tree', [UnidadController::class, 'showTree'])
        ->name('admin.unidades.showTree');
    Route::get('/tree/{unidad}', [UnidadController::class, 'showTree'])
        ->name('admin.unidad.showTree');
});
// , 'can:admin.funcionarios'
Route::prefix('admin/funcionarios')->middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/', [FuncionarioController::class, 'index'])
        ->name('admin.funcionarios.index');

    Route::get('/crear', [FuncionarioController::class, 'create'])
        ->name('admin.funcionarios.create');

    Route::post('/', [FuncionarioController::class, 'store'])
        ->name('admin.funcionarios.store');

    Route::get('/{funcionario}/editar', [FuncionarioController::class, 'edit'])
        ->name('admin.funcionarios.edit');

    Route::put('/{funcionario}', [FuncionarioController::class, 'update'])
        ->name('admin.funcionarios.update');

    Route::delete('/{funcionario}', [FuncionarioController::class, 'destroy'])
        ->name('admin.funcionarios.destroy');

    Route::get('/data', [FuncionarioController::class, 'data'])
        ->name('admin.funcionarios.data');
});

// , 'can:admin.usuarios'
Route::prefix('admin/usuarios')->middleware(['auth', 'verified', 'active'])->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])
        ->name('admin.usuarios.index');

    Route::get('/crear', [UsuarioController::class, 'create'])
        ->name('admin.usuarios.create');

    Route::post('/', [UsuarioController::class, 'store'])
        ->name('admin.usuarios.store');

    Route::get('/{user}/editar', [UsuarioController::class, 'edit'])
        ->name('admin.usuarios.edit');

    Route::put('/{user}', [UsuarioController::class, 'update'])
        ->name('admin.usuarios.update');

    Route::delete('/{user}', [UsuarioController::class, 'destroy'])
        ->name('admin.usuarios.destroy');

    Route::get('/data', [UsuarioController::class, 'data'])
        ->name('admin.usuarios.data');
});
require __DIR__ . '/auth.php';
