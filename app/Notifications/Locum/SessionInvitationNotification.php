<?php

namespace App\Notifications\Locum;

use App\Models\LocumSession;
use App\Models\LocumSessionInvite;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class SessionInvitationNotification extends Notification implements ShouldBroadcast
{
    use Queueable;
    protected $user;
    protected $session;
    protected $sessionInvite;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, LocumSession $session, LocumSessionInvite $sessionInvite)
    {
        $this->user = $user;
        $this->session = $session;
        $this->sessionInvite = $sessionInvite;
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
            ->line('You have been invited for a locum session ' . $this->sessionInvite->title)
            ->line(new HtmlString('<br />'))
            ->line('Please log in to your dashboard to accept or decline the invitation.')
            ->action('Login', url(env('FRONTEND_URL') . '/login'))
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
            'session_invite_id' => $this->sessionInvite->id,
            'session_invite_title' => $this->sessionInvite->title,
            'user_name' => $this->user->profile->first_name . ' ' . $this->user->profile->last_name,
        ];
    }

    public function toBroadcast()
    {
        $notification = [
            "data" => [
                "user_name" => $this->user->profile->first_name . ' ' . $this->user->profile->last_name,
                "session_invite_title" => $this->sessionInvite->title,
            ],
        ];

        return new BroadcastMessage([
            'notification' => $notification,
        ]);
    }
}