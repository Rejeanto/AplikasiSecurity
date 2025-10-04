<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionOption extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function userResponses()
    {
        return $this->hasMany(UserResponse::class, 'selected_option_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
