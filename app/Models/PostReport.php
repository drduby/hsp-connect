<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['post_id', 'user_id', 'reason', 'description', 'status'])]
class PostReport extends Model
{
    public const REASONS = [
        'spam' => 'Spam oder Werbung',
        'harassment' => 'Beleidigung / Hassrede',
        'misinformation' => 'Fehlinformation',
        'medical' => 'Unqualifizierte medizinische Aussagen',
        'offtopic' => 'Nicht community-konform',
        'other' => 'Sonstiges',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
