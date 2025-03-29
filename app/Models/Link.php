<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = [
        'user_id',
        'platform',
        'link',
        'icon'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
