<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // 👈 ADD THIS

class Task extends Model
{
    use HasFactory;

    // ✅ UPDATE this (add user_id, keep others if you use them)
    protected $fillable = [
        'title',
        'description',
        'completed',
        'user_id',
    ];

    // ✅ ADD THIS METHOD (INSIDE THE CLASS)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
