<?php

use App\Http\Controllers\AssignmentHistoryController;
use App\Http\Controllers\CareerAdvisingController;
use App\Http\Controllers\DateRankController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\PamuController;
use App\Http\Controllers\SchoolingsController;
use App\Http\Controllers\SourcedatasController;
use App\Http\Controllers\UnitController;
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
use App\Http\Controllers\SchoolingUnitsController;
use App\Http\Controllers\AwardController;
use App\Http\Controllers\PftController;
use App\Http\Controllers\AwardHistoryController;
use App\Http\Controllers\PftHistoryController;
use App\Http\Controllers\ClassNameController;
use App\Http\Controllers\SchoolingNameController;
use App\Http\Controllers\HomeController;


Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::delete('users/destroy', [UsersController::class, 'massDestroy'])
        ->name('users.massDestroy');
    Route::resource('users', UsersController::class);
    Route::get('/users-list', [UsersController::class, 'list'])->name('users.list');


    Route::delete('permissions/destroy', [PermissionsController::class, 'massDestroy'])
        ->name('permissions.massDestroy');
    Route::get('/permissions/create/{id?}', [PermissionsController::class, 'create'])->name('permissions.create.bulk');
    Route::resource('permissions', PermissionsController::class);
    Route::get('/permissions-list', [PermissionsController::class, 'list'])->name('permissions.list');
    Route::post('/permissions/bulkstore', [PermissionsController::class, 'bulkstore'])->name('permissions.bulkstore');

    Route::delete('roles/destroy', [RolesController::class, 'massDestroy'])
        ->name('roles.massDestroy');
    Route::resource('roles', RolesController::class);
    Route::get('/roles-list', [RolesController::class, 'list'])->name('roles.list');

    Route::delete('audit-trails/destroy', [AuditController::class, 'massDestroy'])
        ->name('audit-trails.massDestroy');
    Route::resource('audit-trails', AuditController::class);
    Route::get('/audit-trails-list', [AuditController::class, 'list'])->name('audit-trails.list');

    Route::delete('assignments/destroy', [AssignmentsController::class, 'massDestroy'])
        ->name('assignments.massDestroy');
    Route::resource('assignments', AssignmentsController::class);
    Route::get('/assignments-list', [AssignmentsController::class, 'list'])->name('assignments.list');

    Route::delete('types/destroy', [TypesController::class, 'massDestroy'])
        ->name('types.massDestroy');
    Route::resource('types', TypesController::class);
    Route::get('/types-list', [TypesController::class, 'list'])->name('types.list');

    Route::delete('ranks/destroy', [RanksController::class, 'massDestroy'])
        ->name('ranks.massDestroy');
    Route::resource('ranks', RanksController::class);
    Route::get('/ranks-list', [RanksController::class, 'list'])->name('ranks.list');

    Route::delete('rankpoints/destroy', [RankpointsController::class, 'massDestroy'])
        ->name('rankpoints.massDestroy');
    Route::resource('rankpoints', RankpointsController::class);
    Route::get('/rankpoints-list', [RankpointsController::class, 'list'])->name('rankpoints.list');

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
    Route::get('/qrsprofiles-list', [QRSProfilesController::class, 'list'])->name('qrsprofiles.list');

    Route::delete('schoolings/destroy', [SchoolingsController::class, 'massDestroy'])
        ->name('schoolings.massDestroy');
    // Route::resource('schoolings', SchoolingsController::class);
    Route::get('/schoolings-list', [SchoolingsController::class, 'list'])->name('schoolings.list');

    Route::delete('schoolingunits/destroy', [SchoolingUnitsController::class, 'massDestroy'])
        ->name('schoolingunits.massDestroy');
    Route::resource('schoolingunits', SchoolingUnitsController::class);
    Route::get('/schoolingunits-list', [SchoolingUnitsController::class, 'list'])->name('schoolingunits.list');

    Route::delete('awards/destroy', [AwardController::class, 'massDestroy'])
        ->name('awards.massDestroy');
    Route::resource('awards', AwardController::class);
    Route::get('/awards-list', [AwardController::class, 'list'])->name('awards.list');

    Route::delete('pfts/destroy', [PftController::class, 'massDestroy'])
        ->name('pfts.massDestroy');
    Route::resource('pfts', PftController::class);
    Route::get('/pfts-list', [PftController::class, 'list'])->name('pfts.list'); // AJAX
    
    // ASSIGNMENT HISTORIES - Fixed routes
    Route::delete('assignmenthistories/destroy', [AssignmentHistoryController::class, 'massDestroy'])
        ->name('assignmenthistories.massDestroy');
    
    // Add-entry route - MUST be before resource routes
    Route::get('assignmenthistories/{assignmenthistory}/add-entry', [AssignmentHistoryController::class, 'createFromExisting'])
        ->name('assignmenthistories.add-entry');
    
    Route::post('/assignmenthistories/store-designation', [AssignmentHistoryController::class, 'storeDesignation'])
        ->name('assignmenthistories.storeDesignation');

    Route::post('/assignmenthistories/store-unit', [AssignmentHistoryController::class, 'storeUnit'])
        ->name('assignmenthistories.storeUnit');
    
    Route::post('/assignmenthistories/compute-year-earned', [AssignmentHistoryController::class, 'computeYearEarned'])
        ->name('assignmenthistories.computeYearEarned');
    
    Route::resource('assignmenthistories', AssignmentHistoryController::class);

    Route::get('/assignmenthistories-list', [AssignmentHistoryController::class, 'list'])->name('assignmenthistories.list');
    
    // // AJAX endpoints
    // Route::post('/assignmenthistories/compute-year-earned', [AssignmentHistoryController::class, 'computeYearEarned'])
    //     ->name('assignmenthistories.computeYearEarned');

    Route::delete('designations/destroy', [DesignationController::class, 'massDestroy'])
        ->name('designations.massDestroy');
    Route::resource('designations', DesignationController::class);
    Route::get('/designations-list', [DesignationController::class, 'list'])->name('designations.list');

    Route::delete('units/destroy', [UnitController::class, 'massDestroy'])
        ->name('units.massDestroy');
    Route::resource('units', UnitController::class);
    Route::get('/units-list', [UnitController::class, 'list'])->name('units.list');

    Route::delete('pamus/destroy', [PamuController::class, 'massDestroy'])
        ->name('pamus.massDestroy');
    Route::resource('pamus', PamuController::class);
    Route::get('/pamus-list', [PamuController::class, 'list'])->name('pamus.list');

    // Route::delete('awardhistories/destroy', [AwardHistoryController::class, 'massDestroy'])
    //     ->name('awardhistories.massDestroy');
    // Route::resource('awardhistories', AwardHistoryController::class);
    // Route::get('/awardhistories-list', [AwardHistoryController::class, 'list'])->name('awardhistories.list');

    // PFT History Routes
    Route::delete('pfthistories/destroy', [PftHistoryController::class, 'massDestroy'])
    ->name('pfthistories.massDestroy');

    // Route::post('pfthistories/{pfthistory}/add-entry', [PftHistoryController::class, 'createFromExisting'])
    // ->name('pfthistories.createFromExisting');

    Route::get('pfthistories/{pfthistory}/add-entry', [PftHistoryController::class, 'createFromExisting'])
    ->name('pfthistories.addEntry');


    Route::get('pfthistories-list', [PftHistoryController::class, 'list'])
    ->name('pfthistories.list');

    Route::get('pfthistories/calc-points', [PftHistoryController::class, 'calculatePftPoints'])
    ->name('pfthistories.calcPoints');

    Route::get('date-ranks/lookup', [PftHistoryController::class, 'lookupRankByDate'])
    ->name('date_ranks.lookupRank');

    Route::resource('pfthistories', PftHistoryController::class)
    ->whereNumber('pfthistory'); // extra safety (optional but recommended)



   
    Route::get('careeradvising/{careeradvising}/create-from-existing', [CareerAdvisingController::class, 'createFromExisting'])
    ->name('careeradvising.createFromExisting');

    Route::delete('careeradvising/destroy', [CareerAdvisingController::class, 'massDestroy'])
        ->name('careeradvising.massDestroy');
    Route::resource('careeradvising', CareerAdvisingController::class);
    Route::get('/careeradvising-list', [CareerAdvisingController::class, 'list'])->name('careeradvising.list');

    Route::delete('dateranks/destroy', [DateRankController::class, 'massDestroy'])
        ->name('dateranks.massDestroy');
    Route::resource('dateranks', DateRankController::class);
    Route::get('/dateranks-list', [DateRankController::class, 'list'])->name('dateranks.list');

    Route::delete('classnames/destroy', [ClassNameController::class, 'massDestroy'])
        ->name('classnames.massDestroy');
    Route::resource('classnames', ClassNameController::class);
    Route::get('/classnames-list', [ClassNameController::class, 'list'])->name('classnames.list');

    Route::delete('schoolingnames/destroy', [SchoolingNameController::class, 'massDestroy'])
        ->name('schoolingnames.massDestroy');

    Route::resource('schoolingnames', SchoolingNameController::class);
    Route::get('/schoolingnames-list', [SchoolingNameController::class, 'list'])->name('schoolingnames.list');

    Route::get('schoolings/ajax/rank-during-completion', [SchoolingsController::class, 'rankDuringCompletion'])
        ->name('schoolings.rankDuringCompletion');

    Route::get('/schoolings/compute-points', [SchoolingsController::class, 'computePoints'])
        ->name('schoolings.computePoints');
    
    Route::get('/schoolings/entries-by-assignment', [SchoolingsController::class, 'getEntriesByAssignment'])
        ->name('schoolings.getEntriesByAssignment');

    Route::get('schoolings/{schooling}/add-entry', [SchoolingsController::class, 'createFromExisting'])
        ->name('schoolings.add-entry');

    Route::resource('schoolings', SchoolingsController::class)
        ->whereNumber('schooling');

    Route::get('officers/bulk/create', [OfficersController::class, 'bulkCreate'])
        ->name('officers.bulkcreate');

    Route::post('officers/bulk/store', [OfficersController::class, 'bulkStore'])
        ->name('officers.bulkstore');

    Route::delete('officers/destroy', [OfficersController::class, 'massDestroy'])
        ->name('officers.massDestroy');

    Route::get('officers/list', [OfficersController::class, 'list'])
        ->name('officers.list');

    Route::resource('officers', OfficersController::class);

    // Route::get('awardhistories/date-rank-at-date', [AwardHistoryController::class, 'dateRankAtDate'])
    //     ->name('awardhistories.dateRankAtDate');

    //     Route::get('awardhistories/{awardhistory}/add-entry', [AwardHistoryController::class, 'createFromExisting'])
    //      ->name('awardhistories.addEntry');

    // Route::get('awardhistories/ajax/award-points', [AwardHistoryController::class, 'pointsForAward'])
    //      ->name('awardhistories.pointsForAward');

    Route::get('awardhistories/date-rank-at-date', [AwardHistoryController::class, 'dateRankAtDate'])
        ->name('awardhistories.dateRankAtDate');
    
    Route::get('awardhistories/ajax/award-points', [AwardHistoryController::class, 'pointsForAward'])
        ->name('awardhistories.pointsForAward');

    Route::get('awardhistories/{awardhistory}/add-entry', [AwardHistoryController::class, 'createFromExisting'])
        ->name('awardhistories.addEntry');

    Route::delete('awardhistories/destroy', [AwardHistoryController::class, 'massDestroy'])
        ->name('awardhistories.massDestroy');
    Route::resource('awardhistories', AwardHistoryController::class);
    Route::get('/awardhistories-list', [AwardHistoryController::class, 'list'])
        ->name('awardhistories.list');


    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    Route::get('/profile/{officer}', [QRSProfilesController::class, 'profile'])->name('profile');
});

Auth::routes();

