<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class SchoolingEntry extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'schooling_entries';
    protected $fillable = [
        'name',
        'schooling_unit_id',
        'assignment_id',
    ];

    public function schoolingunits()
    {
        return $this->belongsTo(SchoolingUnit::class, 'schooling_unit_id', 'id');
    }

    public function assignments()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'id');
    }
}
