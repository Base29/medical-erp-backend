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

class NewHiringRequestNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;
    public $hqUser;
    public $hiringRequest;
    public $requestCreatedBy;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $hqUser, HiringRequest $hiringRequest, User $requestCreatedBy)
    {
        $this->hqUser = $hqUser;
        $this->hiringRequest = $hiringRequest;
        $this->requestCreatedBy = $requestCreatedBy;
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
            ->line('A new hiring request has been created ')
            ->line(new HtmlString('<b>Job Title: ' . $this->hiringRequest->job_title . '</b>'))
            ->line(new HtmlString('<b>Job Contract Type: ' . $this->hiringRequest->contract_type . '</b>'))
            ->line(new HtmlString('<br />'))
            ->line('Please log in to your dashboard to approve or decline the hiring request.')
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
            'hq_user_name' => $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name,
            'hiring_request_id' => $this->hiringRequest->id,
            'title' => $this->hiringRequest->job_title,
            'contract_type' => $this->hiringRequest->contract_type,
            'request_created_by' => $this->requestCreatedBy->profile->first_name . ' ' . $this->requestCreatedBy->profile->last_name,
        ];
    }

    public function toBroadcast()
    {
        $notification = [
            "data" => [
                'hq_user_name' => $this->hqUser->profile->first_name . ' ' . $this->hqUser->profile->last_name,
                'title' => $this->hiringRequest->job_title,
                'contract_type' => $this->hiringRequest->contract_type,
                'request_created_by' => $this->requestCreatedBy->profile->first_name . ' ' . $this->requestCreatedBy->profile->last_name,
            ],
        ];

        return new BroadcastMessage([
            'notification' => $notification,
        ]);
    }
}