<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\AuditController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::delete('users/destroy', [UsersController::class, 'massDestroy'])
        ->name('users.massDestroy');
    Route::resource('users', UsersController::class);
    Route::get('/users-list', [UsersController::class, 'list'])->name('users.list'); // AJAX


    Route::delete('permissions/destroy', [PermissionsController::class, 'massDestroy'])
        ->name('permissions.massDestroy');
    Route::get('/permissions/create/{id?}', [PermissionsController::class, 'create'])->name('permissions.create.bulk');
    Route::resource('permissions', PermissionsController::class);
    Route::get('/permissions-list', [PermissionsController::class, 'list'])->name('permissions.list'); // AJAX
    Route::post('/permissions/bulkstore', [PermissionsController::class, 'bulkstore'])->name('permissions.bulkstore');

    Route::delete('roles/destroy', [RolesController::class, 'massDestroy'])
        ->name('roles.massDestroy');
    Route::resource('roles', RolesController::class);
    Route::get('/roles-list', [RolesController::class, 'list'])->name('roles.list'); // AJAX

    Route::delete('audit-trails/destroy', [AuditController::class, 'massDestroy'])
        ->name('audit-trails.massDestroy');
    Route::resource('audit-trails', AuditController::class);
    Route::get('/audit-trails-list', [AuditController::class, 'list'])->name('audit-trails.list'); // AJAX

});

Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');