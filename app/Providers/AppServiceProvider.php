<?php

namespace App\Providers;

use App\Models\CartItem;
use Illuminate\Support\Facades\View;
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
    public function boot(): void
    {
        // Feeds the little cart-count badge in the shop navbar, on every shop page.
        View::composer('shop.partials.nav', function ($view) {
            $count = auth()->check() && auth()->user()->role === 'buyer'
                ? (int) CartItem::where('user_id', auth()->id())->sum('quantity')
                : 0;

            $view->with('cartCount', $count);
        });
    }
}
