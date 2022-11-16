<?php

namespace App\Notifications\Locum;

use App\Models\LocumSession;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class RemoveLocumFromSessionNotification extends Notification implements
ShouldBroadcast,
ShouldQueue
{
    use Queueable;
    protected $notifiable;
    protected $session;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $notifiable, LocumSession $session)
    {
        $this->notifiable = $notifiable;
        $this->session = $session;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->greeting('Hello! ' . $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name)
            ->line('You have been removed for the locum session ' . $this->session->name)
            ->line(new HtmlString('<br />'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'session_id' => $this->session->id,
            'user_name' => $this->notifiable->profile->first_name . ' ' . $this->notifiable->profile->last_name,
        ];
    }

    public function toBroadcast()
    {
        $notification = [
            "data" => [
                "user_name" => $this->notifiable->profile->first_name . ' ' . $this->notifiable->profile->last_name,
                "session_name" => $this->session->name,
            ],
        ];

        return new BroadcastMessage([
            'notification' => $notification,
        ]);
    }
}