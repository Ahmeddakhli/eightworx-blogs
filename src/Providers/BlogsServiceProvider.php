<?php

namespace eightworx\Blogs\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Nwidart\Modules\Traits\PathNamespace;

class BlogsServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Blogs';

    protected string $nameLower = 'blogs';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {

        // load routes
        Route::prefix('api')
            ->as('api.')
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            });
        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Load views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'blog');

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'blog');
        // $this->registerCommands();
        // $this->registerCommandSchedules();
        // $this->registerTranslations();
        // $this->registerConfig();
        // $this->registerViews();
        // $this->loadMigrationsFrom(module_path($this->name, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        // $this->app->register(EventServiceProvider::class);
        // $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        // $langPath = resource_path('lang/modules/' . $this->nameLower);

        // if (is_dir($langPath)) {
        //     $this->loadTranslationsFrom($langPath, $this->nameLower);
        //     $this->loadJsonTranslationsFrom($langPath);
        // } else {
        //     $this->loadTranslationsFrom(module_path($this->name, 'lang'), $this->nameLower);
        //     $this->loadJsonTranslationsFrom(module_path($this->name, 'lang'));
        // }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        // $this->publishes([module_path($this->name, 'config/config.php') => config_path($this->nameLower . '.php')], 'config');
        // $this->mergeConfigFrom(module_path($this->name, 'config/config.php'), $this->nameLower);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        // $viewPath = resource_path('views/modules/' . $this->nameLower);
        // $sourcePath = module_path($this->name, 'resources/views');

        // $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);

        // $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->nameLower);

        // $componentNamespace = $this->module_namespace($this->name, $this->app_path(config('modules.paths.generator.component-class.path')));
        // Blade::componentNamespace($componentNamespace, $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        // foreach (config('view.paths') as $path) {
        //     if (is_dir($path . '/modules/' . $this->nameLower)) {
        //         $paths[] = $path . '/modules/' . $this->nameLower;
        //     }
        // }

        return $paths;
    }
}
