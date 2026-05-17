<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
   public function boot()
   {
       if ($this->app->environment('production')) {
           \URL::forceScheme('https');
       }

       \Illuminate\Support\Facades\Event::listen(
           \Illuminate\Auth\Events\Login::class,
           function ($event) {
               \Cart::instance('shopping')->restore($event->user->id);
           }
       );

       // Registrar el observador para Order
       \App\Models\Order::observe(\App\Observers\OrderObserver::class);
   }
}
