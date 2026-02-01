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
        'SUFFIX',
        'RANK',
        'AFPSN',
        'AFPOS',
        'TYPE',
        'SIG',
        'SEX',
        'DOR',
        'TYPE',
        'TACS',
        'DOC',
        'DOB',
        'DATE',
        'RET',
        'HCC',
        'SOC',
        'REMARKS',
        'DESIGNATION',
        'UNIT',
    ];
}
