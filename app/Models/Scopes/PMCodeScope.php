<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PMCodeScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        // Standard bypass for login and empty codes
        if (($model instanceof \App\Models\User && request()->routeIs('login')) || is_null($user->pm_code)) {
            return;
        }


        if ($user->hasRole('user')) {
            $builder->where('pm_code', $user->pm_code);
            return; // Exit here so it doesn't apply the pamu filter
        }


        if ($user->hasRole('pamu')) {
            $tableName = $model->getTable();
            static $userPamuId = null;

            if (is_null($userPamuId)) {
                $userPamuId = DB::table('personnel_records')
                    ->where('PM_CODE', $user->pm_code)
                    ->value('pamu_id');
            }

            if (Schema::hasColumn($tableName, 'pamu_id')) {
            
                if ($userPamuId) {
                    $builder->where('pamu_id', $userPamuId);
                }

            } elseif (Schema::hasColumn($tableName, 'pm_code')) {
                $builder->whereIn($tableName . '.pm_code', function ($query) use ($userPamuId) {
                    $query->select('PM_CODE')
                          ->from('personnel_records')
                          ->where('pamu_id', $userPamuId);
                });
            }
        }
    }

}
