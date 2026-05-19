<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $fillable = ['question', 'answer', 'tags', 'sort_order', 'is_published'];

    protected $casts = [
        'tags' => 'array',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query): void
    {
        $query->where('is_published', true);
    }
}
