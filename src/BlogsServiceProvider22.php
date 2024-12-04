<?php

namespace eightworx\Blogs;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
// use Laraveldaily\LaravelPermissionEditor\Http\Middleware\SpatiePermissionMiddleware;

class BlogsServiceProvider22 extends ServiceProvider
{
    public function register()
    {
        // Register package configuration
        // $this->mergeConfigFrom(__DIR__.'/Config/blog.php', 'blog');
        // Register routes
 
    }

    public function boot()
    {
        Route::prefix('api')
        ->as('api.')
        ->group(function () {
            $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        });
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'blog');

        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'blog');

        // // Publish assets
        // $this->publishes([
        //     __DIR__.'/Config/blog.php' => config_path('blog.php'),
        // ], 'config');
        // if ($this->app->runningInConsole()) {
        //     $this->publishes([
        //         __DIR__ . '/Database/Migrations/2020_03_01_091652_create_blogs_table.php' =>
        //         database_path('migrations/' . date('Y_m_d_His', time()) . '_create_blogs_table.php'),
        //         // More migration files here
        //     ], 'migrations');
        // }
       
    }
}
