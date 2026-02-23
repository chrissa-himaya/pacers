<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class PMCodeScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
            if (!auth()->hasUser()) {
                return;
            }
        
            $user = auth()->user();
        
            if ($model instanceof \App\Models\User && request()->routeIs('login')) {
                return;
            }
        
            if (is_null($user->pm_code)) {
                return;
            }
        

            if ($user->pm_code ) {
                $builder->where('pm_code', $user->pm_code);
            } 
    
    }
}
