<?php

namespace App\Providers;

use App\Models\EmployeeHandbook;
use App\Models\HiringRequest;
use App\Models\InterviewSchedule;
use App\Models\ItPolicy;
use App\Models\Offer;
use App\Models\Practice;
use App\Observers\EmployeeHandbook\EmployeeHandbookObserver;
use App\Observers\HiringRequest\HiringRequestObserver;
use App\Observers\Interview\InterviewObserver;
use App\Observers\ItPolicy\ItPolicyObserver;
use App\Observers\Offer\OfferObserver;
use App\Observers\Practice\PracticeObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Practice::observe(PracticeObserver::class);
        EmployeeHandbook::observe(EmployeeHandbookObserver::class);
        ItPolicy::observe(ItPolicyObserver::class);
        Offer::observe(OfferObserver::class);
        HiringRequest::observe(HiringRequestObserver::class);
        InterviewSchedule::observe(InterviewObserver::class);
    }
}