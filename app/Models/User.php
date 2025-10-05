<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'encrypted',
    ];

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is regular user
     */
    public function isUser()
    {
        return $this->role === 'user';
    }

    /**
     * Get user's responses
     */
    public function responses()
    {
        return $this->hasMany(UserResponse::class);
    }

    /**
     * Get user's completed assessments
     */
    public function completedAssessments()
    {
        return Assessment::whereHas('userResponses', function ($query) {
            $query->where('user_id', $this->id);
        })->get()->filter(function ($assessment) {
            return $assessment->isCompletedBy($this->id);
        });
    }
}