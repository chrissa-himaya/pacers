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
        'min_month_rankpoint_id',
        'min_point_rankpoint_id',
        'max_month_rankpoint_id',
        'max_point_rankpoint_id',
    ];

    public function assignments()
    {
        return $this->belongsTo(Assignment::class, 'assignment_id', 'id');
    }

    public function ranks()
    {
        return $this->belongsTo(Rank::class, 'rank_id', 'id');
    }

    public function getRankCodeAttribute(): ?string
    {
        return optional(optional($this->minMonthRankpoint)->ranks)->code
            ?? optional(optional($this->minPointRankpoint)->ranks)->code
            ?? optional(optional($this->maxMonthRankpoint)->ranks)->code
            ?? optional(optional($this->maxPointRankpoint)->ranks)->code;
    }

    public function minMonthRankpoint()
    {
        return $this->belongsTo(Rankpoint::class, 'min_month_rankpoint_id', 'id');
    }
    public function minPointRankpoint()
    {
        return $this->belongsTo(Rankpoint::class, 'min_point_rankpoint_id', 'id');
    }
    public function maxMonthRankpoint()
    {
        return $this->belongsTo(Rankpoint::class, 'max_month_rankpoint_id', 'id');
    }
    public function maxPointRankpoint()
    {
        return $this->belongsTo(Rankpoint::class, 'max_point_rankpoint_id', 'id');
    }
}