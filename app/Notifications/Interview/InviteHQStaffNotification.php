<?php

namespace App\Notifications\Interview;

use App\Models\HiringRequest;
use App\Models\InterviewSchedule;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class InviteHQStaffNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;
    public $hqUser;
    public $interview;
    public $hiringRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $hqUser, HiringRequest $hiringRequest, InterviewSchedule $interview)
    {
        $this->hqUser = $hqUser;
        $this->hiringRequest = $hiringRequest;
        $this->interview = $interview;
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
            ->line('You have been invited to join a interview for ' . $this->hiringRequest->job_title)
            ->line(new HtmlString('<br />'))
            ->line('Link to teams call will be shared in the next email')
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
            'interview_id' => $this->interview->id,
            'interview_title' => $this->hiringRequest->job_title,
            'user_name' => $this->hqUser->profile->first_name . ' ' . $this->hqUser->profile->last_name,
        ];
    }

    public function toBroadcast()
    {
        $notification = [
            "data" => [
                "user_name" => $this->hqUser->profile->first_name . ' ' . $this->hqUser->profile->last_name,
                "interview_title" => $this->hiringRequest->job_title,
            ],
        ];

        return new BroadcastMessage([
            'notification' => $notification,
        ]);
    }
}