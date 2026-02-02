<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class PftHistory extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'pfthistories';
    protected $fillable = [
        'pm_code',
        'entry',
        'rating',
        'date_taken',
        'supervising_unit',
        'rank',
        'age',
        'profile',
    ];
}
