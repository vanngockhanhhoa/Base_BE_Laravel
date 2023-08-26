<?php

namespace App\Providers;

use Helper\LogHelperService;
use Illuminate\Support\ServiceProvider;

/**
 * Class LogHelperServiceProvider
 * @package App\Providers
 */
class LogHelperServiceProvider extends ServiceProvider
{
    /**
     * register provider
     */
    public function register()
    {
        $this->app->singleton(LogHelperService::class);
        $this->app->alias(LogHelperService::class, 'loghelper');
    }
}
