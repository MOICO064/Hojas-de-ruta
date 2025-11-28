<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnidadController;

Route::get('/', function () {
    return view('layouts.app');
});

Route::get('/dashboard', function () {
    return view('admin.index');
})->middleware(['auth', 'verified'])->name('dashboard');

//  ->middleware(['auth', 'verified', 'can:admin.unidades'])
Route::prefix('admin/unidades')->group(function () {
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
});


require __DIR__ . '/auth.php';
