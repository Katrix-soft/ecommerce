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
               // 1. Guardar temporalmente el carrito de invitado (sesión actual)
               $guestCart = \Cart::instance('shopping')->content();
               
               // 2. Restaurar el carrito guardado del usuario (esto sobreescribe la sesión actual)
               \Cart::instance('shopping')->restore($event->user->id);
               
               // 3. Volver a agregar los productos que tenía como invitado
               $restoredCart = \Cart::instance('shopping')->content();
               foreach ($guestCart as $item) {
                   if ($restoredCart->has($item->rowId)) {
                       // Si el producto ya estaba en su carrito guardado, usamos la cantidad que eligió como invitado
                       \Cart::instance('shopping')->update($item->rowId, $item->qty);
                   } else {
                       // Si no estaba, lo agregamos
                       \Cart::instance('shopping')->add([
                           'id' => $item->id,
                           'name' => $item->name,
                           'qty' => $item->qty,
                           'price' => $item->price,
                           'options' => $item->options ? $item->options->toArray() : []
                       ]);
                   }
               }
               
               // 4. Actualizar la base de datos con el carrito combinado
               try {
                   \DB::table('shoppingcart')->where('identifier', $event->user->id)->delete();
               } catch (\Exception $e) {}
               
               \Cart::instance('shopping')->store($event->user->id);
           }
       );

       // Registrar el observador para Order
       \App\Models\Order::observe(\App\Observers\OrderObserver::class);
   }
}
