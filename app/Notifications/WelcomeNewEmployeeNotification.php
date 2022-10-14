<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class WelcomeNewEmployeeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $credentials;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(array $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->greeting('Welcome! ' . $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name)
            ->line('Your account is successfully created. Please use the below mentioned password to Sign In')
            ->line(new HtmlString('<br />'))
            ->line(new HtmlString('<strong>Password: ' . $this->credentials['password'] . '</strong>'))
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
            //
        ];
    }
}