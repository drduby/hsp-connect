<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PostRated extends Notification
{
    use Queueable;

    public function __construct(
        public readonly User $rater,
        public readonly Post $post,
        public readonly int $rating,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'rater_nickname' => $this->rater->nickname,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'rating' => $this->rating,
        ];
    }
}
