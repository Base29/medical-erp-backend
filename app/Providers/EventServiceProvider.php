<?php

namespace App\Providers;

use App\Models\EmployeeHandbook;
use App\Models\ItPolicy;
use App\Models\Practice;
use App\Observers\EmployeeHandbook\EmployeeHandbookObserver;
use App\Observers\ItPolicy\ItPolicyObserver;
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
    }
}