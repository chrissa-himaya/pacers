<?php

namespace App\Models;

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
        'assignment_id',
        'rank_id',
        'rankpoint_id',
    ];
}
