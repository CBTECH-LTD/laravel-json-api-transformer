<?php

namespace CbtechLtd\JsonApiTransformer;

use Illuminate\Support\ServiceProvider;

class JsonApiTransformerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('json-api-transformer.php'),
            ], 'config');

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'json-api-transformer');

        // Register the main class to use with the facade
        $this->app->singleton('json-api-transformer', function () {
            return new JsonApiTransformer;
        });
    }
}
