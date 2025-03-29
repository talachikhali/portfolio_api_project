<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'bio',
        'image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function projects(){
        return $this->hasMany(Project::class);
    }

    public function testimonials(){
        return $this->hasMany(Testimonial::class);
    }

    public function skills(){
        return $this->hasMany(Skill::class);
    }

    public function services(){
        return $this->hasMany(Service::class);
    }

    public function links(){
        return $this->hasMany(Link::class);
    }

    public function sentMessage(){
        return $this->hasMany(Message::class , 'sender_id');
    }

    public function receivedMessages(){
        return $this->hasMany(Message::class, 'recipient_id');
    }
}
