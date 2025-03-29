<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'icon'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
