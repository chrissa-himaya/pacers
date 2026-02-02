<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Unit extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'units';
    protected $fillable = [
        'name',
        'pamu_id',
    ];

    public function pamus()
    {
        return $this->belongsTo(Pamu::class, 'pamu_id', 'id');
    }
}
