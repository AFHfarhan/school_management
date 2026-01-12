<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\TeacherAuthEventListener;

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
        // register teacher auth event listeners for login/logout
        Event::listen(Login::class, [TeacherAuthEventListener::class, 'handleLogin']);
        Event::listen(Logout::class, [TeacherAuthEventListener::class, 'handleLogout']);
    }
}
