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
    ];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'pm_code', 'PM_CODE');
    }

    public function schoolingentries()
    {
        return $this->belongsTo(SchoolingEntry::class, 'schooling_entries_id', 'id');
    }

    public function schoolingunits()
    {
        return $this->belongsTo(SchoolingUnit::class, 'schooling_unit_id', 'id');
    }

    public function assignments()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'id');
    }
}