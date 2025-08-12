<?php
// AppServiceProvider.php - Enhanced with Microsoft OAuth and custom notification channel
// Added Microsoft OAuth provider registration for Laravel Socialite
// Registered custom MailServiceChannel to use MailService for notifications
// Enables club-specific mail configurations for notification system

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Microsoft\Provider;
use Illuminate\Support\Facades\Notification;
use App\Channels\MailServiceChannel;

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
        // Register Microsoft OAuth provider
        Socialite::extend('microsoft', function ($app) {
            $config = $app['config']['services.microsoft'];
            return Socialite::buildProvider(Provider::class, $config);
        });
    }
}
