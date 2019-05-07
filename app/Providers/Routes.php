<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class Routes extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes within the package.
     *
     * @var array
     */
    protected $packages = [
        // 'Example' => 'App\Example',
    ];

    /**
     * Define the routes for the application.
     */
    public function map()
    {
        foreach ($this->packages as $package => $namespace) {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($namespace.'\Apis')
                ->group(app_path($package.'/Routes/api.php'));

            Route::middleware('web')
                ->namespace($namespace.'\Controllers')
                ->group(app_path($package.'/Routes/web.php'));
        }
    }
}
