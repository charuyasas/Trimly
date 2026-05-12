<?php

namespace App\Providers;

use App\SMS\Contracts\SmsServiceInterface;
use App\SMS\Services\TextifysSmsService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SmsServiceInterface::class, TextifysSmsService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
