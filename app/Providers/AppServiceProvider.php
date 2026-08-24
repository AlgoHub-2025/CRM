<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::before(function ($user, $ability) {
            return $user->hasRole(['CEO', 'Managing Director']) ? true : null;
        });

        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'employee' => \App\Models\Employee::class,
            'client' => \App\Models\Client::class,
        ]);
    }
}
