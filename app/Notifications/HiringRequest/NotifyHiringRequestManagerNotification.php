<?php

namespace App\Notifications\HiringRequest;

use App\Models\HiringRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class NotifyHiringRequestManagerNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;
    public $hiringRequest;
    public $manager;
    public $hqUser;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $manager, HiringRequest $hiringRequest, User $hqUser)
    {
        $this->manager = $manager;
        $this->hiringRequest = $hiringRequest;
        $this->hqUser = $hqUser;
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
            ->subject('Hiring Request ' . ucfirst($this->hiringRequest->status))
            ->greeting('Hello! ' . $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name)
            ->line('Your hiring request has been ' . $this->hiringRequest->status)
            ->line(new HtmlString('<b>Job Title: ' . $this->hiringRequest->job_title . '</b>'))
            ->line(new HtmlString('<b>Job Contract Type: ' . $this->hiringRequest->contract_type . '</b>'))
            ->line(new HtmlString('<br />'))
            ->line('Please log in to your dashboard to check the status of your hiring request.')
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
            'recruiter_user_name' => $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name,
            'hiring_request_id' => $this->hiringRequest->id,
            'title' => $this->hiringRequest->job_title,
            'contract_type' => $this->hiringRequest->contract_type,
            'request_action_by' => $this->hqUser->profile->first_name . ' ' . $this->hqUser->profile->last_name,
        ];
    }

    public function toBroadcast()
    {
        $notification = [
            "data" => [
                'manager_user_name' => $this->manager->profile->first_name . ' ' . $this->manager->profile->last_name,
                'title' => $this->hiringRequest->job_title,
                'contract_type' => $this->hiringRequest->contract_type,
                'request_approved_by' => $this->hqUser->profile->first_name . ' ' . $this->hqUser->profile->last_name,
            ],
        ];

        return new BroadcastMessage([
            'notification' => $notification,
        ]);
    }
}