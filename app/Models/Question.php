<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_TEXT_INPUT = 'text_input';


    public function questionOptions()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function assessmentSteps()
    {
        return $this->belongsToMany(Step::class, 'step_questions', 'question_id', 'assessment_step_id')
            ->withPivot('order')
            ->withTimestamps();
    }

    public function userResponses()
    {
        return $this->hasMany(UserResponse::class);
    }

    public function scopeMultipleChoice($query)
    {
        return $query->where('question_type', self::TYPE_MULTIPLE_CHOICE);
    }

    public function scopeTextInput($query)
    {
        return $query->where('question_type', self::TYPE_TEXT_INPUT);
    }

    public function isMultipleChoice()
    {
        return $this->question_type === self::TYPE_MULTIPLE_CHOICE;
    }

    public function isTextInput()
    {
        return $this->question_type === self::TYPE_TEXT_INPUT;
    }

    public static function getQuestionTypes()
    {
        return [
            self::TYPE_MULTIPLE_CHOICE => 'Pilihan Ganda',
            self::TYPE_TEXT_INPUT => 'Input Teks',
        ];
    }
}
