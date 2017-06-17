<?php
namespace lroman242\LaravelSettings;

use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * This provider is deferred and should be lazy loaded.
     *
     * @var boolean
     */
    protected $defer = true;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'laravel-settings');
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'laravel-settings');

        $this->publishes([
            __DIR__ . '/lang' => resource_path('lang/vendor/laravel-settings'),
        ], 'laravel-settings');

        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('settings.php'),
        ], 'laravel-settings');

        $this->publishes([
            __DIR__ . '/migrations' => database_path('migrations'),
        ], 'laravel-settings');
    }

    /**
     * Register bindings.
     */
    public function register()
    {
        $this->app->singleton('lroman242\LaravelSettings\SettingsManager', function ($app) {
            return new SettingsManager($app);
        });

        $this->app->singleton('lroman242\LaravelSettings\SettingsStorage', function ($app) {
            return $app->make('lroman242\LaravelSettings\SettingsManager')->driver();
        });

        $this->app->singleton('Settings', function ($app) {
            return new Settings($app->make('lroman242\LaravelSettings\SettingsStorage'));
        });
    }

    /**
     * Which bindings the provider provides.
     *
     * @return array
     */
    public function provides()
    {
        return ['Settings', 'lroman242\LaravelSettings\SettingsStorage', 'lroman242\LaravelSettings\SettingsManager'];
    }
}
