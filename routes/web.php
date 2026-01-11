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
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\AnulacionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificacionFController;
use App\Http\Controllers\BackupController;
use Google\Client;

Route::get('/', function () {
    return redirect('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified', 'active'])->name('dashboard');

Route::prefix('admin/unidades')->middleware(['auth', 'verified', 'active', 'can:admin.unidades'])->group(function () {
    Route::get('/', [UnidadController::class, 'index'])->name('admin.unidades.index');
    Route::get('/crear', [UnidadController::class, 'create'])->name('admin.unidades.create')->middleware('can:admin.unidades.create');
    Route::post('/', [UnidadController::class, 'store'])->name('admin.unidades.store')->middleware('can:admin.unidades.create');
    Route::get('/{unidad}/editar', [UnidadController::class, 'edit'])->name('admin.unidades.edit')->middleware('can:admin.unidades.edit');
    Route::put('/{unidad}', [UnidadController::class, 'update'])->name('admin.unidades.update')->middleware('can:admin.unidades.edit');
    Route::delete('/{unidad}', [UnidadController::class, 'destroy'])->name('admin.unidades.destroy')->middleware('can:admin.unidades.destroy');
    Route::get('/data', [UnidadController::class, 'data'])
        ->name('admin.unidades.data')->middleware('can:admin.unidades.data');
    Route::get('/tree', [UnidadController::class, 'showTree'])->name('admin.unidades.showTree')->middleware('can:admin.unidades.organigrama');
    Route::get('/tree/{unidad}', [UnidadController::class, 'showTree'])->name('admin.unidad.showTree')->middleware('can:admin.unidad.organigrama');
});

Route::prefix('admin/funcionarios')->middleware(['auth', 'verified', 'active', 'can:admin.funcionarios'])->group(function () {
    Route::get('/', [FuncionarioController::class, 'index'])->name('admin.funcionarios.index');
    Route::get('/crear', [FuncionarioController::class, 'create'])->name('admin.funcionarios.create')->middleware('can:admin.funcionarios.create');
    Route::post('/', [FuncionarioController::class, 'store'])->name('admin.funcionarios.store')->middleware('can:admin.funcionarios.create');
    Route::get('/{funcionario}/editar', [FuncionarioController::class, 'edit'])->name('admin.funcionarios.edit')->middleware('can:admin.funcionarios.edit');
    Route::put('/{funcionario}', [FuncionarioController::class, 'update'])->name('admin.funcionarios.update')->middleware('can:admin.funcionarios.edit');
    Route::delete('/{funcionario}', [FuncionarioController::class, 'destroy'])->name('admin.funcionarios.destroy')->middleware('can:admin.funcionarios.destroy');
    Route::get('/data', [FuncionarioController::class, 'data'])->name('admin.funcionarios.data')->middleware('can:admin.funcionarios.data');
});

Route::prefix('admin/usuarios')->middleware(['auth', 'verified', 'active', 'can:admin.usuarios'])->group(function () {
    Route::get('/', [UsuarioController::class, 'index'])->name('admin.usuarios.index');
    Route::get('/crear', [UsuarioController::class, 'create'])->name('admin.usuarios.create')->middleware('can:admin.usuarios.create');
    Route::post('/', [UsuarioController::class, 'store'])->name('admin.usuarios.store')->middleware('can:admin.usuarios.create');
    Route::get('/{user}/editar', [UsuarioController::class, 'edit'])->name('admin.usuarios.edit')->middleware('can:admin.usuarios.edit');
    Route::put('/{user}', [UsuarioController::class, 'update'])->name('admin.usuarios.update')->middleware('can:admin.usuarios.edit');
    Route::delete('/{user}', [UsuarioController::class, 'destroy'])->name('admin.usuarios.destroy')->middleware('can:admin.usuarios.destroy');
    Route::get('/data', [UsuarioController::class, 'data'])->name('admin.usuarios.data')->middleware('can:admin.usuarios.data');
});

Route::prefix('admin/roles-permisos')->middleware(['auth', 'verified', 'active', 'can:admin.roles.permisos'])->group(function () {
    Route::get('/roles', [RolesAndPermisosController::class, 'rolesIndex'])->name('admin.roles.index');
    Route::get('/roles/crear', [RolesAndPermisosController::class, 'rolesCreate'])->name('admin.roles.create')->middleware('can:admin.roles.create');
    Route::post('/roles', [RolesAndPermisosController::class, 'rolesStore'])->name('admin.roles.store')->middleware('can:admin.roles.create');
    Route::get('/roles/{role}/editar', [RolesAndPermisosController::class, 'rolesEdit'])->name('admin.roles.edit')->middleware('can:admin.roles.edit');
    Route::put('/roles/{role}', [RolesAndPermisosController::class, 'rolesUpdate'])->name('admin.roles.update')->middleware('can:admin.roles.edit');
    Route::delete('/roles/{role}', [RolesAndPermisosController::class, 'rolesDestroy'])->name('admin.roles.destroy')->middleware('can:admin.roles.destroy');
    Route::get('/roles/data', [RolesAndPermisosController::class, 'rolesData'])->name('admin.roles.data')->middleware('can:admin.roles.data');
    Route::get('/roles/{role}/permisos', [RolesAndPermisosController::class, 'permisos'])->name('admin.roles.permisos')->middleware('can:admin.roles.permisos');
    Route::post('/roles/{role}/permisos/{permission}', [RolesAndPermisosController::class, 'togglePermiso'])->name('admin.roles.permisos.toggle')->middleware('can:admin.roles.permisos.activar.desactivar');
    Route::post('/roles/{role}/asignar/permisos', [RolesAndPermisosController::class, 'asignarTodos'])->middleware('can:admin.roles.permisos.asignar');
    Route::post('/roles/{role}/quitar/permisos', [RolesAndPermisosController::class, 'quitarTodos'])->middleware('can:admin.roles.permisos.quitar');

    Route::get('/permisos', [RolesAndPermisosController::class, 'permisosIndex'])->name('admin.permisos.index');
    Route::get('/permisos/crear', [RolesAndPermisosController::class, 'permisosCreate'])->name('admin.permisos.create')->middleware('can:admin.permisos');
    Route::post('/permisos', [RolesAndPermisosController::class, 'permisosStore'])->name('admin.permisos.store')->middleware('can:admin.permisos.create');
    Route::get('/permisos/{permission}/editar', [RolesAndPermisosController::class, 'permisosEdit'])->name('admin.permisos.edit')->middleware('can:admin.permisos.edit');
    Route::put('/permisos/{permission}', [RolesAndPermisosController::class, 'permisosUpdate'])->name('admin.permisos.update')->middleware('can:admin.permisos.edit');
    Route::delete('/permisos/{permission}', [RolesAndPermisosController::class, 'permisosDestroy'])->name('admin.permisos.destroy')->middleware('can:admin.permisos.destroy');
    Route::get('/permisos/data', [RolesAndPermisosController::class, 'permisosData'])->name('admin.permisos.data')->middleware('can:admin.permisos.data');
});


Route::prefix('admin/hojaruta')->middleware(['auth', 'verified', 'active', 'can:admin.hojaruta'])->group(function () {

    Route::get('/{gestion?}/gestion', [HojaRutaController::class, 'index'])->name('admin.hojaruta.index')->middleware('can:admin.hojaruta.index');
    Route::get('/crear', [HojaRutaController::class, 'create'])->name('admin.hojaruta.create')->middleware('can:admin.hojaruta.create');
    Route::post('/', [HojaRutaController::class, 'store'])->name('admin.hojaruta.store')->middleware('can:admin.hojaruta.create');
    Route::get('/{hoja}/editar', [HojaRutaController::class, 'edit'])->name('admin.hojaruta.edit')->middleware('can:admin.hojaruta.edit');
    Route::put('/{hoja}', [HojaRutaController::class, 'update'])->name('admin.hojaruta.update')->middleware('can:admin.hojaruta.edit');
    Route::get('/data/{gestion?}', [HojaRutaController::class, 'data'])->name('admin.hojaruta.data')->middleware('can:admin.hojaruta.data');
    Route::get('/{hoja}', [HojaRutaController::class, 'show'])->name('admin.hojaruta.show')->middleware('can:admin.hojaruta.show');
    Route::get('/todos/funcionarios', [HojaRutaController::class, 'funcionarios'])->middleware('can:admin.hojaruta.funcionarios');
    Route::post('/{id}/concluir', [HojaRutaController::class, 'concluir'])->name('hojaruta.concluir')->middleware('can:admin.hojaruta.concluir');

});
Route::prefix('admin/hojaruta/{hoja}/derivaciones')->middleware(['auth', 'verified', 'active', 'can:admin.derivaciones'])->group(function () {

    Route::get('/', [DerivacionController::class, 'index'])->name('admin.derivaciones.index');
    Route::get('/crear', [DerivacionController::class, 'create'])->name('admin.derivaciones.create')->middleware('can:admin.derivaciones.create');
    Route::post('/', [DerivacionController::class, 'store'])->name('admin.derivaciones.store')->middleware('can:admin.derivaciones.create');
    Route::get('/{derivacion}/editar', [DerivacionController::class, 'edit'])->name('admin.derivaciones.edit')->middleware('can:admin.derivaciones.edit');
    Route::put('/{derivacion}', [DerivacionController::class, 'update'])->name('admin.derivaciones.update')->middleware('can:admin.derivaciones.edit');
    Route::get('/data', [DerivacionController::class, 'data'])->name('admin.derivaciones.data')->middleware('can:admin.derivaciones.data');
    Route::get('/{derivacion}', [DerivacionController::class, 'show'])->name('admin.derivaciones.show')->middleware('can:admin.derivaciones.show');
    Route::get('/{derivacion}/print', [DerivacionController::class, 'show'])->name('admin.derivaciones.print')->middleware('can:admin.derivaciones.print');
});
Route::post('/admin/derivaciones/{id}/recepcionar', [DerivacionController::class, 'recepcionar'])->name('derivaciones.recepcionar')->middleware('can:admin.derivaciones.recepcionar');
Route::get('/admin/derivaciones/{unidad}/funcionarios', [DerivacionController::class, 'funcionarios'])->name('admin.derivaciones.funcionarios')->middleware('auth');

Route::prefix('admin/buzon')->middleware(['auth', 'verified', 'active', 'can:admin.buzon'])->group(function () {

    Route::get('/entrada', [BuzonEntradaController::class, 'index'])->name('admin.buzon.entrada');
    Route::get('/entrada/data', [BuzonEntradaController::class, 'data'])->name('admin.buzon.entrada.data');
    Route::get('/salida', [BuzonSalidaController::class, 'index'])->name('admin.buzon.salida');
    Route::get('/salida/data', [BuzonSalidaController::class, 'data'])->name('admin.buzon.salida.data');
});


Route::prefix('admin/reportes')->middleware(['auth', 'verified', 'active', 'can:admin.reportes'])->group(function () {

    Route::get('/hoja-ruta/{id}', [ReportesController::class, 'hojaRuta'])->name('admin.reportes.hoja-ruta');
    Route::get('/derivaciones', [ReportesController::class, 'derivaciones'])->name('admin.reportes.derivaciones');
    Route::get('/consultar-hoja', [ReportesController::class, 'consultarHoja'])->name('admin.reportes.consultar-hoja')->middleware('can:admin.reportes.consultar-hoja');
    Route::post('/consultar-hoja', [ReportesController::class, 'buscarHoja'])->name('admin.reportes.consultar-hoja-ajax')->middleware('can:admin.reportes.consultar-hoja');
    Route::get('/hojas-por-unidad', [ReportesController::class, 'hojasPorUnidad'])->name('admin.reportes.hojas-por-unidad')->middleware('can:admin.reportes.hojas-por-unidad');
    Route::post('/hojas-por-unidad-ajax', [ReportesController::class, 'hojasPorUnidadAjax'])->name('admin.reportes.hojas-por-unidad-ajax')->middleware('can:admin.reportes.hojas-por-unidad');
    Route::get('/total-unidades', [ReportesController::class, 'totalUnidades'])->name('admin.reportes.total-unidades')->middleware('can:admin.reportes.total-unidades');
    Route::post('/total-unidades', [ReportesController::class, 'hojasPorGestion'])->name('admin.reportes.hojas-total-unidades-ajax')->middleware('can:admin.reportes.total-unidades');

});


Route::prefix('admin/anulaciones')->middleware(['auth', 'verified', 'active', 'can:admin.anulaciones'])->group(function () {

    Route::post('/store', [AnulacionController::class, 'store'])->name('admin.anulaciones.store');
    Route::get('/show', [AnulacionController::class, 'show'])->name('admin.anulaciones.show');

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


Route::prefix('notificaciones')->middleware('auth')->group(function () {

    Route::get('/', [NotificacionFController::class, 'index'])->name('notificaciones.index');
    Route::get('/todas', [NotificacionFController::class, 'todas'])->name('notificaciones.todas');
    Route::post('/marcar-todas-leidas', [NotificacionFController::class, 'marcarTodasLeidas'])->name('notificaciones.marcarTodasLeidas');
    Route::get('/no-leidas', [NotificacionFController::class, 'noLeidas'])->name('notificaciones.noLeidas');
    Route::get('/ver/{id}', [NotificacionFController::class, 'marcarLeida'])->name('notificaciones.leida');

});

Route::middleware(['auth', 'verified', 'can:admin.backup.full'])->prefix('admin')->group(function () {
    Route::get('/admin/backup/full', [BackupController::class, 'full'])
        ->name('admin.backup.full');
});

Route::get('/consultar/hoja', [ReportesController::class, 'consultarHojaCiudadano'])->name('consultar.hoja');
Route::post('/consultar/hoja', [ReportesController::class, 'buscarHoja'])->name('consultar.hoja.resultado');

require __DIR__ . '/auth.php';