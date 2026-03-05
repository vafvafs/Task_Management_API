<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;


class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'profile_photo_path'];

    /** Team has many users (team_leader + regular users in that team). */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
