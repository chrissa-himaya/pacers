<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Type extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'types';
    protected $fillable = [
        'name',
    ];
}
