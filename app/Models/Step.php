<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Step extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'step_number' => 'integer',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'step_questions', 'assessment_step_id', 'question_id')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('step_questions.order');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('step_number');
    }

    public function getNextStepAttribute()
    {
        return $this->assessment->steps()
            ->where('step_number', '>', $this->step_number)
            ->orderBy('step_number')
            ->first();
    }

    public function getPreviousStepAttribute()
    {
        return $this->assessment->steps()
            ->where('step_number', '<', $this->step_number)
            ->orderBy('step_number', 'desc')
            ->first();
    }

    public function isFirstStep()
    {
        return $this->step_number === 1;
    }

    public function isLastStep()
    {
        return $this->step_number === $this->assessment->total_steps;
    }

}
