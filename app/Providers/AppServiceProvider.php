<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;

use App\Models\AssignmentHistory;
use App\Observers\AssignmentHistoryObserver;

use App\Models\Sourcedata;
use App\Observers\SourcedataObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        AssignmentHistory::observe(AssignmentHistoryObserver::class);
        Sourcedata::observe(SourcedataObserver::class);
    }
}
