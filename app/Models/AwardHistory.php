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
        'award_id',
        'award_type',
        'date',
        'go_number',
        'date_rank_id',
        'points',
    ];

    public function officer()
    {
        return $this->belongsTo(Officer::class, 'pm_code', 'PM_CODE');
    }

    public function designations()
    {
        return $this->belongsTo(Designation::class, 'designation_id', 'id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
    public function dateranks()
    {
        return $this->belongsTo(DateRank::class, 'date_rank_id', 'id');
    }

    public function awards()
    {
        return $this->belongsTo(Award::class, 'award_id', 'id');
    }

    public function awardType()
    {
        return $this->belongsTo(Award::class, 'award_type', 'id');
    }
    public function getRankAtDateAttribute(): string
    {
        return $this->dateranks?->ranks?->code ?? '';
    }

    protected $appends = ['rank_at_date'];
}