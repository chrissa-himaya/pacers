<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class ClassName extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'classnames';
    protected $fillable = [
        'name',
        'year',
    ];

    protected $attributes = [
        'name' => 'Class',
    ];
}
