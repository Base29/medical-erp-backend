<?php

namespace App\Notifications\Offer;

use App\Models\HiringRequest;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OfferDeclinedCandidateNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;
    public $offer;
    public $candidate;
    public $hiringRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Offer $offer, User $candidate, HiringRequest $hiringRequest)
    {
        $this->offer = $offer;
        $this->candidate = $candidate;
        $this->hiringRequest = $hiringRequest;
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
            ->subject('Offer Declined')
            ->greeting('Hello! ' . $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name)
            ->line('Offer has been been declined for ' . $this->hiringRequest->job_title)
            ->line(new HtmlString('<b>Job Title: ' . $this->hiringRequest->job_title . '</b>'))
            ->line(new HtmlString('<b>Job Contract Type: ' . $this->hiringRequest->contract_type . '</b>'))
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
            'hiring_request_title' => $this->hiringRequest->job_title,
            'hiring_request_id' => $this->hiringRequest->id,
            'candidate_name' => $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name,
            'offer_id' => $this->offer->id,
            'offer_joining_date' => $this->offer->joining_date,
            'offer_amount' => $this->offer->amount,
            'offer_status' => $this->offer->status,
        ];
    }

    public function toBroadcast()
    {
        $notification = [
            "data" => [
                'hiring_request_title' => $this->hiringRequest->job_title,
                'hiring_request_id' => $this->hiringRequest->id,
                'candidate_name' => $this->candidate->profile->first_name . ' ' . $this->candidate->profile->last_name,
                'offer_id' => $this->offer->id,
                'offer_joining_date' => $this->offer->joining_date,
                'offer_amount' => $this->offer->amount,
                'offer_status' => $this->offer->status,
            ],
        ];

        return new BroadcastMessage([
            'notification' => $notification,
        ]);
    }
}