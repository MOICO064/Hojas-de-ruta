<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserActive;
use Illuminate\Support\Facades\View;
use App\Models\HojaRuta;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Route::aliasMiddleware('active', CheckUserActive::class);
        View::composer('components.sidebar', function ($view) {
            $gestiones = HojaRuta::select('gestion')->distinct()->orderByDesc('gestion')->pluck('gestion');
            $view->with('gestiones', $gestiones);
        });
    }
}
