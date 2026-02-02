<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class AwardHistory extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'awardhistories';
    protected $fillable = [
        'pm_code',
        'entry',
        'type',
        'date',
        'go_number',
        'rank',
        'points',
    ];
}
