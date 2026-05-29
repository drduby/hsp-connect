<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['post_id', 'user_id', 'reason', 'description', 'status'])]
class PostReport extends Model
{
    public const REASONS = ['spam', 'harassment', 'misinformation', 'medical', 'offtopic', 'other'];

    public static function translatedReasons(): array
    {
        return collect(self::REASONS)->mapWithKeys(fn (string $key) => [
            $key => __('ui.report_post.reason_'.$key),
        ])->all();
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
