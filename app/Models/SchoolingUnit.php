<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class SchoolingUnit extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'schooling_units';
    protected $fillable = [
        'name',
        'location',
    ];
}
