<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class DateRank extends Model implements Auditable
{
    use AuditableTrait;
    
    public $table = 'date_ranks';
    
    protected $fillable = [
        'pm_code',
        'rank_id',
        'date',
    ];

    /**
     * Cast attributes to native types
     */
    // protected $casts = [
    //     'date' => 'date',
    // ];

    /**
     * Relationship: DateRank belongs to Rank
     */
    public function ranks()
    {
        return $this->belongsTo(Rank::class, 'rank_id', 'id');
    }
}