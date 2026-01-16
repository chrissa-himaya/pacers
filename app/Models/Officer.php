<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Officer extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'personnel_records';

    protected $fillable = [
        'SRTY',
        'PM_CODE',
        'NAME',
        'RANK',
        'AFPSN',
        'AFPOS',
        'SIG',
        'DOR',
        'TACS',
        'DOC',
        'DOB',
        'DATE',
        'RET',
        'HCC',
        'SOC',
        'REMARKS',
        'LAST_NAME',
        'FIRST_NAME',
        'MID_INITIAL',
        'SUFFIX',
    ];
}
