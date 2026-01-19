<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class SchoolUnit extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'school_units';
    protected $fillable = [
        'name',
        'location',
    ];
}
