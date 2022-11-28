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
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class NewOfferCreatedCandidateNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;
    public $candidate;
    public $offer;
    public $hiringRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $candidate, Offer $offer, HiringRequest $hiringRequest)
    {
        $this->candidate = $candidate;
        $this->offer = $offer;
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
            ->subject('Received New Offer For ' . $this->hiringRequest->job_title)
            ->greeting('Hello! ' . $notifiable->profile->first_name . ' ' . $notifiable->profile->last_name)
            ->line('A new offer has been made to you for ' . $this->hiringRequest->job_title)
            ->line(new HtmlString('<b>Job Title: ' . $this->hiringRequest->job_title . '</b>'))
            ->line(new HtmlString('<b>Job Contract Type: ' . $this->hiringRequest->contract_type . '</b>'))
            ->line(new HtmlString('<b>Your Joining Date: ' . Carbon::createFromFormat('Y-m-d', $this->offer->joining_date)->toFormattedDateString() . '</b>'))
            ->line(new HtmlString('<b>Offered Amount: &#8356;' . $this->offer->amount . '/hr</b>'))
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
            ],
        ];

        return new BroadcastMessage([
            'notification' => $notification,
        ]);
    }
}