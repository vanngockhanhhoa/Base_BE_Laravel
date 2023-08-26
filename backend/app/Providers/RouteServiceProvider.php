<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';
    protected string $apiNamespace = 'App\Http\Controllers\Api';
    protected string $adminsNameSpace = 'App\Http\Controllers\Api';
    protected string $tllNameSpace = 'App\Http\Controllers\Api\TLLincon';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api/v1')
                ->group(base_path('routes/api.php'));

            // Start API v1
            // split module router for each actor
            Route::group(['prefix' => 'api/v1', 'middleware' => 'api'], function () {
                Route::prefix('admins')
                    ->namespace($this->adminsNameSpace)
                    ->group(base_path('routes/api/v1/admins.php')); // Admin
                Route::prefix('tll')
                    ->namespace($this->tllNameSpace)
                    ->group(base_path('routes/api/v1/tll.php')); // TLLincoln
            });
            // End API v1

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(60)->by($request->input('email').$request->ip()),
            ];
        });
    }
}
