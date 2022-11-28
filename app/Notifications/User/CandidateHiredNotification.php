<?php

namespace App\Notifications\User;

use App\Models\HiringRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CandidateHiredNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $hiringRequest;
    public $hiringManager;
    public $candidate;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(HiringRequest $hiringRequest, User $hiringManager, User $candidate)
    {
        $this->hiringRequest = $hiringRequest;
        $this->hiringManager = $hiringManager;
        $this->candidate = $candidate;
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
            ->subject('Candidate Hired for ' . $this->hiringRequest->job_title)
            ->greeting('Hello! ' . $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name)
            ->line('A candidate has been hired for ' . $this->hiringRequest->job_title)
            ->line(new HtmlString('<b>Job Title: ' . $this->hiringRequest->job_title . '</b>'))
            ->line(new HtmlString('<b>Job Contract Type: ' . $this->hiringRequest->contract_type . '</b>'))
            ->line(new HtmlString('<b>Candidate Name: ' . $this->candidate->profile->first_name . ' ' . $this->candidate->profile->last_name . '</b>'))
            ->line(new HtmlString('<br />'))
            ->line('Please log in to your dashboard to create an induction for the candidate')
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
            'hiring_request_title' => $this->hiringRequest->job_title,
            'hiring_request_id' => $this->hiringRequest->id,
            'candidate_name' => $this->candidate->profile->first_name . ' ' . $this->candidate->profile->last_name,
            'manager_name' => $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name,
        ];
    }

    public function toBroadcast()
    {
        $notification = [
            "data" => [
                'hiring_request_title' => $this->hiringRequest->job_title,
                'hiring_request_id' => $this->hiringRequest->id,
                'candidate_name' => $this->candidate->profile->first_name . ' ' . $this->candidate->profile->last_name,
                'manager_name' => $this->hiringManager->profile->first_name . ' ' . $this->hiringManager->profile->last_name,
            ],
        ];

        return new BroadcastMessage([
            'notification' => $notification,
        ]);
    }
}