<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * Task model: title, description, completed, owner (user_id). scopeVisibleTo restricts by role for listing.
 */
class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'completed',
        'user_id',
    ];

    
    protected $casts = [
        'completed' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Restrict query to tasks the user is allowed to see: admin=all, team_leader=team's tasks, user=own only.
     * Used by TaskController::index so visibility logic lives in one place.
     */
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->isAdmin()) {
            return $query;
        }
        if ($user->isTeamLeader()) {
            return $query->whereHas('user', fn ($q) => $q->where('team_id', $user->team_id));
        }
        return $query->where('user_id', $user->id);
    }
}
