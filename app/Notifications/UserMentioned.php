<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserMentioned extends Notification
{
    use Queueable;

    public function __construct(
        public readonly User $mentioner,
        public readonly Post $post,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'mentioner_nickname' => $this->mentioner->nickname,
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
        ];
    }
}
