<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'profficiency'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function projects() {
        return $this->belongsToMany(Project::class);
    }
}
