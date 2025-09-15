<?php

namespace App\Providers;

use App\Events\PaymentCreated;
use App\Events\PaymentEvent;
use App\Listeners\NotifyPaymentCreated;
use App\Listeners\PaymentAccepted;
use App\Models\Loan;
use App\Observers\LoanObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        PaymentCreated::class => [
            NotifyPaymentCreated::class
        ],
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
        Loan::observe(LoanObserver::class);
    }
}
