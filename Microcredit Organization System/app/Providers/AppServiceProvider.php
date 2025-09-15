<?php

namespace App\Providers;

use App\Services\OsonSms;
use App\View\Components\Forms\SelectDay;
use App\View\Components\Forms\SelectMonth;
use App\View\Components\Forms\SelectYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Paginator::useBootstrap();

        $this->app->bind('sms', function($app) {
            return new OsonSms(
                env('OSONSMS_LOGIN'),
                env('OSONSMS_HASH'),
                env('OSONSMS_SENDER'),
                env('OSONSMS_SERVER'),
                env('OSONSMS_TRX_PREFIX')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::component('select-day', SelectDay::class);
        Blade::component('select-month', SelectMonth::class);
        Blade::component('select-year', SelectYear::class);

        Blade::directive('date', function ($expression) {
            return "<?php echo date('Y-m-d',$expression); ?>";
        });

        Blade::directive('date_printer', function ($expression) {
            return "<?php echo date('«d» m Y',$expression); ?>";
        });
    }
}
