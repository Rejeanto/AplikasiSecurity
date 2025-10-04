<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserResponse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function questionOption()
    {
        return $this->belongsTo(QuestionOption::class);
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAssessment($query, $assessmentId)
    {
        return $query->where('assessment_id', $assessmentId);
    }

    public function getFormattedAnswerAttribute()
    {
        if ($this->question->isMultipleChoice()) {
            return $this->selectedOption ? $this->selectedOption->option_text : '-';
        }
        
        return $this->answer_text ?: '-';
    }

    public function isMultipleChoiceResponse()
    {
        return $this->selected_option_id !== null;
    }

    public function isTextInputResponse()
    {
        return $this->answer_text !== null;
    }
}
