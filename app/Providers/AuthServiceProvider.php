<?php

namespace App\Providers;

use App\Policies\UserPolicy;
use Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }

    protected $policies = [
        User::class => \App\Policies\UserPolicy::class,
    ];
}
