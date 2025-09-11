<?php

namespace App\Providers;

use App\Http\Resources\CartProductResource;
use App\Http\Resources\CategoryResource;
use App\Models\Cart;
use App\Models\Category;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Inertia::share([
            'categories' => CategoryResource::collection(Category::all()),
            'cartProduct' => function () {
                if (auth()->check()) {
                    return CartProductResource::collection(Cart::where('user_id', auth()->id())->latest()->get());
                }

                return 0;
            }



        ]);
    }
}
