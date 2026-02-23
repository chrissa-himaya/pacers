<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

use App\Models\Scopes\PMCodeScope;

class CareerAdvising extends Model implements Auditable

{
    use AuditableTrait;
    public $table = 'career_advising_records';
    protected $fillable = [
        'pm_code',
        'date_of_advise',
        'career_adviser',
        'mode_of_coms',
        'venue',
        'remarks',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\PMCodeScope);
    }

}
