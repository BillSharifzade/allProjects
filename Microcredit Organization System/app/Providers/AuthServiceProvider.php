<?php

namespace App\Providers;

use App\Models\Loan;
use App\Models\Note;
use App\Models\Payment;
use App\Models\User;
use App\Policies\LoanPolicy;
use App\Policies\NotePolicy;
use App\Policies\PaymentPolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Loan::class => LoanPolicy::class,
        Payment::class => PaymentPolicy::class,
        Note::class => NotePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('admin-loan-index', function (User  $user) {
           return $user->isAdmin()
               ? Response::allow()
               : Response::deny('Доступ закрыт');
        });

        Gate::define('view-archive', function (User $user) {
            return $user->isAdmin()
                ? Response::allow()
                : Response::deny('Доступ закрыт');
        });
    }
}
