<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Task;
use App\Models\Team;


class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    
    public const ROLE_ADMIN = 'admin';
    public const ROLE_TEAM_LEADER = 'team_leader';
    public const ROLE_USER = 'user';

    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'team_id',
        'profile_photo_path',
    ];

  
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /** Tasks owned by this user. */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeamLeader(): bool
    {
        return $this->role === self::ROLE_TEAM_LEADER;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }
}
