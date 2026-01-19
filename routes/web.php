<?php

use App\Http\Controllers\SourcedatasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\AssignmentsController;
use App\Http\Controllers\TypesController;
use App\Http\Controllers\RanksController;
use App\Http\Controllers\RankpointsController;
use App\Http\Controllers\OfficersController;
use App\Http\Controllers\QRSProfilesController;



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

    Route::delete('assignments/destroy', [AssignmentsController::class, 'massDestroy'])
        ->name('assignments.massDestroy');
    Route::resource('assignments', AssignmentsController::class);
    Route::get('/assignments-list', [AssignmentsController::class, 'list'])->name('assignments.list'); // AJAX

    Route::delete('types/destroy', [TypesController::class, 'massDestroy'])
        ->name('types.massDestroy');
    Route::resource('types', TypesController::class);
    Route::get('/types-list', [TypesController::class, 'list'])->name('types.list'); // AJAX

    Route::delete('ranks/destroy', [RanksController::class, 'massDestroy'])
        ->name('ranks.massDestroy');
    Route::resource('ranks', RanksController::class);
    Route::get('/ranks-list', [RanksController::class, 'list'])->name('ranks.list'); // AJAX

    Route::delete('rankpoints/destroy', [RankpointsController::class, 'massDestroy'])
        ->name('rankpoints.massDestroy');
    Route::resource('rankpoints', RankpointsController::class);
    Route::get('/rankpoints-list', [RankpointsController::class, 'list'])->name('rankpoints.list'); // AJAX

    Route::delete('sourcedatas/destroy', [SourcedatasController::class, 'massDestroy'])
        ->name('sourcedatas.massDestroy');
    Route::resource('sourcedatas', SourcedatasController::class);
    Route::get('/sourcedatas-list', [SourcedatasController::class, 'list'])->name('sourcedatas.list'); // AJAX

    Route::delete('officers/destroy', [OfficersController::class, 'massDestroy'])
        ->name('officers.massDestroy');
    Route::resource('officers', OfficersController::class);
    Route::get('/officers-list', [OfficersController::class, 'list'])->name('officers.list'); // AJAX

    Route::delete('qrsprofiles/destroy', [QRSProfilesController::class, 'massDestroy'])
        ->name('qrsprofiles.massDestroy');
    Route::resource('qrsprofiles', QRSProfilesController::class);
    Route::get('/qrsprofiles-list', [QRSProfilesController::class, 'list'])->name('qrsprofiles.list'); // AJAX

});

Auth::routes();

Route::get('/dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');