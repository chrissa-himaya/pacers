<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Assignment extends Model implements Auditable
{
    use AuditableTrait;
    public $table = 'assignments';
    protected $fillable = [
        'name',
        'type_id',
    ];

    public function types()
    {
        return $this->belongsTo(Type::class, 'type_id', 'id');
    }
}
