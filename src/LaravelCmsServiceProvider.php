<?php

namespace AlexStack\LaravelCms;

use Illuminate\Support\ServiceProvider;

class LaravelCmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        // routes
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-cms');

        // Load translation
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'laravel-cms');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Publish configuration file
        $this->publishes([__DIR__.'/config/laravel-cms.php' => config_path('laravel-cms.php')], 'laravel-cms-config');

        // Publish admin view
        $this->publishes([__DIR__.'/resources/views' => base_path('resources/views/vendor/laravel-cms')], 'laravel-cms-views');

        // Publish language files
        $this->publishes([__DIR__.'/resources/lang' => base_path('resources/lang/vendor/laravel-cms')], 'laravel-cms-lang');

        // Publish public files and assets.
        $this->publishes([__DIR__.'/assets' => public_path('laravel-cms')], 'laravel-cms-assets');

        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\Commands\LaravelCMS::class,
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/laravel-cms.php',
            'laravel-cms'
        );

        // Config Repository
    }
}
