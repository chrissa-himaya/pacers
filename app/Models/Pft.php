<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Pft extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'pfts';
    protected $fillable = [
        'ranks_id',
        'points',
    ];

    public function ranks()
    {
        return $this->belongsTo(Rank::class, 'ranks_id', 'id');
    }
}
