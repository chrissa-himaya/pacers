<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Audit extends Model
{
    public $table = 'audits';
    protected $fillable = [
        'name',
        'email',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => Carbon::parse($value)->format('Y-M-d H:i:s'),
        );
    }  

}
