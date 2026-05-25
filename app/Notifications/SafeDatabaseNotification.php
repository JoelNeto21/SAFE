<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SafeDatabaseNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $title,
        protected string $body,
        protected ?string $url = null,
        protected string $category = 'info',
        protected string $color = 'primary',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'url' => $this->url,
            'category' => $this->category,
            'color' => $this->color,
        ];
    }
}
