<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Rankpoint extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'rankpoints';
    protected $fillable = [
        'rank_id',
        'name',
        'points',
    ];

    public function ranks()
    {
        return $this->belongsTo(Rank::class, 'rank_id', 'id');
    }
}