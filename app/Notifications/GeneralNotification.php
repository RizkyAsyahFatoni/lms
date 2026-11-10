<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    public array $payload;

    /**
     * Create a new notification instance.
     *
     * @param array $payload [
     *   'title'    => string,
     *   'message'  => string,
     *   'url'      => string|null,
     *   'category' => string (course, webinar, certificate, evaluation, approval, profile, system),
     *   'icon'     => string (graduation-cap, video, award, clipboard, building, user, bell)
     * ]
     */
    public function __construct(array $payload)
    {
        $this->payload = array_merge([
            'title'    => 'Pemberitahuan Sistem',
            'message'  => '',
            'url'      => '#',
            'category' => 'system',
            'icon'     => 'bell',
        ], $payload);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return $this->payload;
    }
}
