<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Assignment;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Carbon\Carbon;

class AssignmentHistory extends Model implements Auditable

{
    use AuditableTrait;
    public $table = 'assignment_histories';
    protected $fillable = [
        'pm_code',
        'designation_id',
        'unit_id',
        'subunit',
        'pamu_id',
        'assignment_id',
        'pri_sec_spec',
        'assignment_type',
        'geography',
        'start_date',
        'end_date',
        'rank_during_completion',
        'year_earned',
        'computed_points',
        'points_last_recomputed_at',

    ];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'pm_code', 'PM_CODE');
    }
    public function designations()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function pamus()
    {
        return $this->belongsTo(Pamu::class, 'pamu_id', 'id');
    }

    public function assignments()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'id');
    }

    public function assignmentType()
    {
        return $this->belongsTo(Assignment::class, 'assignment_type', 'id');
    }

}