<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FacebookAds\FacebookAdsAccount;
use Illuminate\Contracts\Support\DeferrableProvider;

class FacebookAdsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FacebookAdsAccount::class, function () {
            return new FacebookAdsAccount;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [FacebookAdsAccount::class];
    }
}
