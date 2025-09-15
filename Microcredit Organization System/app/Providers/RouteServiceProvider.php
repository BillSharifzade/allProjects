<?php

namespace App\Providers;

use App\Models\Cashbox;
use App\Models\CashboxUser;
use App\Models\GoldPrice;
use App\Models\InterestRate;
use App\Models\Loan;
use App\Models\Note;
use App\Models\Payment;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });

        Route::bind('loan', function ($value){
            return Loan::where('id', $value)->firstOrFail();
        });

        Route::bind('payment', function ($value){
            return Payment::where('id', $value)->firstOrFail();
        });

        Route::bind('cashbox', function ($value){
            return Cashbox::where('id', $value)->firstOrFail();
        });

        Route::bind('cashboxUser', function($value){
            return CashboxUser::where('id', $value)->firstOrFail();
        });

        Route::bind('interestRate', function($value){
            return InterestRate::where('id', $value)->firstOrFail();
        });

        Route::bind('goldPrice', function($value){
            return GoldPrice::where('id', $value)->firstOrFail();
        });

        Route::bind('note', function($value){
            return Note::where('id', $value)->firstOrFail();
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
