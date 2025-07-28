<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MunicipioController;
use App\Http\Controllers\ColegioController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Ruta de bienvenida (accesible sin login)
Route::get('/', function () {
    return view('welcome');
});

// Rutas del formulario de denuncia pública (accesible sin login)
Route::get('/denuncia', [ReportController::class, 'create'])->name('report.create');
Route::post('/denuncia', [ReportController::class, 'store'])->name('report.store');
Route::get('/denuncia/gracias', function () {
    return view('reports.success');
})->name('report.success');


// Rutas Protegidas por Autenticación (Panel Administrativo)
// Este es el grupo principal que *requiere* que el usuario esté logueado
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard principal (Ver Denuncias)
    Route::get('/dashboard', [ReportController::class, 'index'])->name('dashboard');
    Route::get('/denuncias/{report}', [ReportController::class, 'show'])->name('reports.show');
    Route::post('/denuncias/{report}/update-status', [ReportController::class, 'updateStatus'])->name('reports.updateStatus');
    Route::post('/denuncias/{report}/add-seguimiento', [ReportController::class, 'addSeguimiento'])->name('reports.addSeguimiento');


    // --- Rutas específicas para Super Administrador ---
    // Route::middleware('role:super_admin')->group(function () {
    Route::group([], function () {
        // Gestión de Usuarios
        Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/admin/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::patch('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Gestión de Municipios
        Route::get('/admin/municipios', [MunicipioController::class, 'index'])->name('municipios.index');
        Route::get('/admin/municipios/create', [MunicipioController::class, 'create'])->name('municipios.create');
        Route::post('/admin/municipios', [MunicipioController::class, 'store'])->name('municipios.store');
        Route::get('/admin/municipios/{municipio}/edit', [MunicipioController::class, 'edit'])->name('municipios.edit');
        Route::patch('/admin/municipios/{municipio}', [MunicipioController::class, 'update'])->name('municipios.update');
        Route::delete('/admin/municipios/{municipio}', [MunicipioController::class, 'destroy'])->name('municipios.destroy');

        // Gestión de Colegios
        Route::get('/admin/colegios', [ColegioController::class, 'index'])->name('colegios.index');
        Route::get('/admin/colegios/create', [ColegioController::class, 'create'])->name('colegios.create');
        Route::post('/admin/colegios', [ColegioController::class, 'store'])->name('colegios.store');
        Route::get('/admin/colegios/{colegio}/edit', [ColegioController::class, 'edit'])->name('colegios.edit');
        Route::patch('/admin/colegios/{colegio}', [ColegioController::class, 'update'])->name('colegios.update');
        Route::delete('/admin/colegios/{colegio}', [ColegioController::class, 'destroy'])->name('colegios.destroy');

        Route::get('/admin/reports', function () { return view('admin.reports.index'); })->name('admin.reports.index');
    });


    // --- Rutas específicas para Administrador ---
    // Route::middleware('role:admin')->group(function () {
    Route::group([], function () {
        Route::get('/admin/my-users/create', [UserController::class, 'createSupervisor'])->name('my-users.create-supervisor');
        Route::post('/admin/my-users', [UserController::class, 'storeSupervisor'])->name('my-users.store-supervisor');
        Route::get('/admin/denuncias-entidad', [ReportController::class, 'indexByAdmin'])->name('reports.indexByAdmin');
        Route::get('/admin/denuncias-entidad/{report}', [ReportController::class, 'showByAdmin'])->name('reports.showByAdmin');
        Route::get('/admin/reports-entidad', function () { return view('admin.reports.entity_index'); })->name('admin.reports.entity_index');
    });


    // --- Rutas específicas para Supervisor ---
    // Route::middleware('role:supervisor')->group(function () {
    Route::group([], function () {
        Route::get('/admin/denuncias-supervisor', [ReportController::class, 'indexBySupervisor'])->name('reports.indexBySupervisor');
        Route::get('/admin/denuncias-supervisor/{report}', [ReportController::class, 'showBySupervisor'])->name('reports.showBySupervisor');
    });


    // Rutas de perfil de usuario (también protegidas por 'auth')
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

}); // Fin del grupo de rutas protegidas por 'auth' y 'verified'

require __DIR__.'/auth.php';