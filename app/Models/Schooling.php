<?php

namespace App\Models;

use App\Models\Assignment;
use App\Models\SchoolingEntry;
use App\Models\SchoolUnit;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Schooling extends Model implements Auditable

{
    use AuditableTrait;
    public $table = 'schoolings';
    protected $fillable = [
        'pm_code',
        'schooling_entries_id',
        'school_unit_id',
        'assignment_id',
        'date_completed',
        'rating',
        'standing',
        'total_student',
        'rank_during_completion',
        '2lt',
        '1lt',
        'cpt',
        'ltc',
        'col',
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
        return $this->belongsTo(SchoolUnit::class, 'school_unit_id', 'id');
    }

    public function assignments()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'id');
    }
}
