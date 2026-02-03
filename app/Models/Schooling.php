<?php

namespace App\Models;

use App\Models\Assignment;
use App\Models\SchoolingEntry;
use App\Models\SchoolingUnit;
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
        'schoolingname_id',
        'classname_id',
        'schooling_unit_id',
        'assignment_id',
        'date_completed',
        'rating',
        'standing',
        'total_student',
        'rank_during_completion',
        '2lt',
        '1lt',
        'cpt',
        'maj',
        'ltc',
        'col',
    ];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'pm_code', 'PM_CODE');
    }

    public function schoolingnames()
    {
        return $this->belongsTo(SchoolingName::class, 'schoolingname_id', 'id');
    }

    public function classnames()
    {
        return $this->belongsTo(ClassName::class, 'classname_id', 'id');
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
