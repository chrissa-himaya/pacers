<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class DesigUnit extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'designation_units';
    protected $fillable = [
        'designation',
        'unit',
        'pamu',
        'pa_equivalent',
        'geography',
    ];
}
