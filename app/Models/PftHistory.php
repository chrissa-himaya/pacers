<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use App\Models\Scopes\PMCodeScope;

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
        'points',
    ];

    // protected $casts = [
    //     'date_taken' => 'date',
    //     'rating' => 'float',
    //     'points' => 'float',
    // ];

    /**
     * Get the officer associated with this PFT record
     */
    public function officer()
    {
        return $this->belongsTo(Officer::class, 'pm_code', 'PM_CODE');
    }

    /**
     * Get the rank record
     */
    public function rankRecord()
    {
        return $this->belongsTo(Rank::class, 'rank', 'code');
    }

    protected static function booted()
    {
        static::addGlobalScope(new \App\Models\Scopes\PMCodeScope);
    }

}