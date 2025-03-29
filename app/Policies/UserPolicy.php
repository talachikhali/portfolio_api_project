<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        
    }
    public function owner(User $user, $model){
        return $user->id === $model->user_id;
    }
}
