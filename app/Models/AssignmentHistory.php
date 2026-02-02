<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Assignment;
use App\Models\SchoolingEntry;
use App\Models\SchoolingUnit;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class AssignmentHistory extends Model implements Auditable

{
    use AuditableTrait;
    public $table = 'assignment_histories';
    protected $fillable = [
        'pm_code',
        'entry',
        'unit',
        'pamu',
        'category',
        'pri_sec_spec',
        'assignment_type',
        'geography',
        'start_date',
        'end_date',
        'rank_during_completion',
        'year',
    ];
}