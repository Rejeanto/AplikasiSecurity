<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'total_steps' => 'integer',
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function steps()
    {
        return $this->hasMany(Step::class);
    }

    public function userResponses()
    {
        return $this->hasMany(UserResponse::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTotalQuestionsAttribute()
    {
        return $this->steps->sum(function ($step) {
            return $step->questions->count();
        });
    }

    public function isCompletedBy($userId)
    {
        $totalQuestions = $this->total_questions;
        $answeredQuestions = $this->userResponses()
            ->where('user_id', $userId)
            ->count();

        return $totalQuestions > 0 && $answeredQuestions >= $totalQuestions;
    }
}
