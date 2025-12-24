<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolesAndPermisosController;
use App\Http\Controllers\HojaRutaController;
use App\Http\Controllers\BuzonEntradaController;
use App\Http\Controllers\DerivacionController;
use App\Http\Controllers\BuzonSalidaController;
use Google\Client;

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

Route::prefix('admin/roles-permisos')->middleware(['auth', 'verified', 'active'])->group(function () {

    // =================================
    //             ROLES
    // =================================

    // Listado de roles
    Route::get('/roles', [RolesAndPermisosController::class, 'rolesIndex'])
        ->name('admin.roles.index');

    // Crear rol
    Route::get('/roles/crear', [RolesAndPermisosController::class, 'rolesCreate'])
        ->name('admin.roles.create');

    Route::post('/roles', [RolesAndPermisosController::class, 'rolesStore'])
        ->name('admin.roles.store');

    // Editar rol
    Route::get('/roles/{role}/editar', [RolesAndPermisosController::class, 'rolesEdit'])
        ->name('admin.roles.edit');

    Route::put('/roles/{role}', [RolesAndPermisosController::class, 'rolesUpdate'])
        ->name('admin.roles.update');

    Route::delete('/roles/{role}', [RolesAndPermisosController::class, 'rolesDestroy'])
        ->name('admin.roles.destroy');

    Route::get('/roles/data', [RolesAndPermisosController::class, 'rolesData'])
        ->name('admin.roles.data');

    Route::get('/roles/{role}/permisos', [RolesAndPermisosController::class, 'permisos'])
        ->name('admin.roles.permisos');
    // Acción del switch (toggle)
    Route::post('/roles/{role}/permisos/{permission}', [RolesAndPermisosController::class, 'togglePermiso'])
        ->name('admin.roles.permisos.toggle');
    Route::post('/roles/{role}/asignar/permisos', [RolesAndPermisosController::class, 'asignarTodos']);
    Route::post('/roles/{role}/quitar/permisos', [RolesAndPermisosController::class, 'quitarTodos']);

    // =================================
    //            PERMISOS
    // =================================

    // Vista principal (lista infinita)
    Route::get('/permisos', [RolesAndPermisosController::class, 'permisosIndex'])
        ->name('admin.permisos.index');

    // Crear permiso
    Route::get('/permisos/crear', [RolesAndPermisosController::class, 'permisosCreate'])
        ->name('admin.permisos.create');

    Route::post('/permisos', [RolesAndPermisosController::class, 'permisosStore'])
        ->name('admin.permisos.store');

    // Editar permiso
    Route::get('/permisos/{permission}/editar', [RolesAndPermisosController::class, 'permisosEdit'])
        ->name('admin.permisos.edit');

    Route::put('/permisos/{permission}', [RolesAndPermisosController::class, 'permisosUpdate'])
        ->name('admin.permisos.update');

    // Eliminar permiso
    Route::delete('/permisos/{permission}', [RolesAndPermisosController::class, 'permisosDestroy'])
        ->name('admin.permisos.destroy');

    Route::get('/permisos/data', [RolesAndPermisosController::class, 'permisosData'])
        ->name('admin.permisos.data');
});


Route::prefix('admin/hojaruta')->middleware(['auth', 'verified', 'active'])->group(function () {

    Route::get('/{gestion?}/gestion', [HojaRutaController::class, 'index'])
        ->name('admin.hojaruta.index');
    Route::get('/crear', [HojaRutaController::class, 'create'])
        ->name('admin.hojaruta.create');
    Route::post('/', [HojaRutaController::class, 'store'])
        ->name('admin.hojaruta.store');
    Route::get('/{hoja}/editar', [HojaRutaController::class, 'edit'])
        ->name('admin.hojaruta.edit');
    Route::put('/{hoja}', [HojaRutaController::class, 'update'])
        ->name('admin.hojaruta.update');
    Route::delete('/{hoja}', [HojaRutaController::class, 'destroy'])
        ->name('admin.hojaruta.destroy');
    Route::get('/data/{gestion?}', [HojaRutaController::class, 'data'])
        ->name('admin.hojaruta.data');
    Route::get('/{hoja}', [HojaRutaController::class, 'show'])
        ->name('admin.hojaruta.show');
    Route::get('{unidad}/funcionarios', function ($unidad) {
        return \App\Models\Funcionario::where('unidad_id', $unidad)
            ->select('id', 'nombre')
            ->get();
    });
});
Route::prefix('admin/hojaruta/{hoja}/derivaciones')
    ->middleware(['auth', 'verified', 'active'])
    ->group(function () {


        Route::get('/', [DerivacionController::class, 'index'])
            ->name('admin.derivaciones.index');

        Route::get('/crear', [DerivacionController::class, 'create'])
            ->name('admin.derivaciones.create');

        Route::post('/', [DerivacionController::class, 'store'])
            ->name('admin.derivaciones.store');

        Route::get('/{derivacion}/editar', [DerivacionController::class, 'edit'])
            ->name('admin.derivaciones.edit');

        Route::put('/{derivacion}', [DerivacionController::class, 'update'])
            ->name('admin.derivaciones.update');

        Route::delete('/{derivacion}', [DerivacionController::class, 'destroy'])
            ->name('admin.derivaciones.destroy');

        Route::get('/data', [DerivacionController::class, 'data'])
            ->name('admin.derivaciones.data');

        Route::get('/{derivacion}', [DerivacionController::class, 'show'])
            ->name('admin.derivaciones.show');
        Route::get('/{derivacion}/print', [DerivacionController::class, 'show'])
            ->name('admin.derivaciones.print');
    });


Route::prefix('admin/buzon')
    ->middleware(['auth', 'verified', 'active'])
    ->group(function () {

        Route::get('/entrada', [BuzonEntradaController::class, 'index'])
            ->name('admin.buzon.entrada');

        Route::get('/entrada/data', [BuzonEntradaController::class, 'data'])
            ->name('admin.buzon.entrada.data');

        Route::get('/salida', [BuzonSalidaController::class, 'index'])
            ->name('admin.buzon.salida');

        Route::get('/salida/data', [BuzonSalidaController::class, 'data'])
            ->name('admin.buzon.salida.data');
    });
Route::get('/token-drive', function () {
    $client = new Client();
    $client->setAuthConfig(storage_path('app/google/credenciales.json'));
    $client->setScopes(['https://www.googleapis.com/auth/drive.file']);

    $token = $client->fetchAccessTokenWithAssertion();

    return response()->json([
        'access_token' => $token['access_token'],
        'expires_in' => $token['expires_in']
    ]);
})->middleware('auth');

require __DIR__ . '/auth.php';