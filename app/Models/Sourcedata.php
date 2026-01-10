<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Sourcedata extends Model implements Auditable

{
    use AuditableTrait;
    public $table = 'sourcedatas';
    protected $fillable = [
        'assignment_id',
        'rank_id',
        'rankpoint_id',
    ];

    public function assignments()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'id');
    }

    public function types()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }

    public function ranks()
    {
        return $this->belongsTo(Rank::class, 'rank_id', 'id');
    }

    public function rankpoints()
    {
        return $this->belongsTo(Rankpoint::class, 'rankpoint_id', 'id');
    }
}