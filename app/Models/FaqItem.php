<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $fillable = ['question', 'question_en', 'answer', 'answer_en', 'tags', 'sort_order', 'is_published'];

    public function getLocalizedQuestionAttribute(): string
    {
        if (app()->getLocale() === 'en' && ! empty($this->question_en)) {
            return $this->question_en;
        }

        return $this->question;
    }

    public function getLocalizedAnswerAttribute(): string
    {
        if (app()->getLocale() === 'en' && ! empty($this->answer_en)) {
            return $this->answer_en;
        }

        return $this->answer;
    }

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query): void
    {
        $query->where('is_published', true);
    }
}
