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

class SessionInviteAcceptedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;
    protected $user;
    protected $session;
    protected $sessionInvite;
    protected $creator;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, LocumSession $session, LocumSessionInvite $sessionInvite, User $creator)
    {
        $this->user = $user;
        $this->session = $session;
        $this->sessionInvite = $sessionInvite;
        $this->creator = $creator;
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
            ->line($this->user->profile->first_name . ' ' . $this->user->profile->last_name . ' have accepted the invitation for the locum session ' . $this->sessionInvite->title)
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
            'session_invite_id' => $this->sessionInvite->id,
            'session_invite_title' => $this->sessionInvite->title,
            'user_name' => $this->user->profile->first_name . ' ' . $this->user->profile->last_name,
            'creator_name' => $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name,
        ];
    }

    public function toBroadcast()
    {
        $notification = [
            "data" => [
                "user_name" => $this->user->profile->first_name . ' ' . $this->user->profile->last_name,
                "session_invite_title" => $this->sessionInvite->title,
                "creator_name" => $this->creator->profile->first_name . ' ' . $this->creator->profile->last_name,
            ],
        ];

        return new BroadcastMessage([
            'notification' => $notification,
        ]);
    }
}