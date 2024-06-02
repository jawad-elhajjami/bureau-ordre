<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use  Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);

        User::deleting(function ($user) {
            if ($user->non_deletable) {
                throw new \Exception('This user cannot be deleted.');
            }
        });

    }
}
