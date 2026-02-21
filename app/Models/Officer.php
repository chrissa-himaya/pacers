<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use App\Models\Designation;
use App\Models\Unit;
use App\Models\Role;
use App\Models\AssignmentHistory;
use App\Models\Schooling;
use App\Models\AwardHistory;
use App\Models\PftHistory;

class Officer extends Model implements Auditable
{
    use AuditableTrait;

    public $table = 'personnel_records';

    protected $fillable = [
        'SRTY',
        'PM_CODE',
        'NAME',
        'SUFFIX',
        'RANK',
        'AFPSN',
        'AFPOS',
        'TYPE',
        'SIG',
        'SEX',
        'DOR',
        'TACS',
        'DOC',
        'DOB',
        'RET',
        'HCC',
        'SOC',
        'REMARKS',
        'designation_id',
        'unit_id',
        'role_id',
    ];

    public function designations()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(AssignmentHistory::class, 'pm_code', 'PM_CODE');
    }

    public function schoolings()
    {
        return $this->hasMany(Schooling::class, 'pm_code', 'PM_CODE');
    }

    public function awards()
    {
        return $this->hasMany(AwardHistory::class, 'pm_code', 'PM_CODE');
    }

    public function pfts()
    {
        return $this->hasMany(PftHistory::class, 'pm_code', 'PM_CODE');
    }

}
