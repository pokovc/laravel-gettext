<?php

namespace pokovc\LaravelGettext;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use pokovc\LaravelGettext\Adapters\AdapterInterface;
use pokovc\LaravelGettext\Config\ConfigManager;
use pokovc\LaravelGettext\Config\Models\Config;

/**
 * Main service provider.
 *
 * Class LaravelGettextServiceProvider
 * @package pokovc\LaravelGettext
 *
 */
class LaravelGettextServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../../config/config.php' => config_path('laravel-gettext.php')
        ], 'config');
    }

    /**
     * Register the service provider.
     *
     * @return mixed
     */
    public function register()
    {
        $configuration = ConfigManager::create();

        $this->app->bind(
            AdapterInterface::class,
            $configuration->get()->getAdapter()
        );

        $this->app->singleton(Config::class, function ($app) use ($configuration) {
            return $configuration->get();
        });

        // Main class register
        $this->app->singleton(LaravelGettext::class, function (Application $app) use ($configuration) {
            $fileSystem = new FileSystem($configuration->get(), app_path(), storage_path());
            $storage = $app->make($configuration->get()->getStorage());

            if ('symfony' == $configuration->get()->getHandler()) {
                // symfony translator implementation
                $translator = new Translators\Symfony(
                    $configuration->get(),
                    $this->app->make(AdapterInterface::class),
                    $fileSystem,
                    $storage
                );
            } else {
                // GNU/Gettext php extension
                $translator = new Translators\Gettext(
                    $configuration->get(),
                    $this->app->make(AdapterInterface::class),
                    $fileSystem,
                    $storage
                );
            }

            return new LaravelGettext($translator);
        });
        $this->app->alias(LaravelGettext::class, 'laravel-gettext');

        // Alias
        $this->app->booting(function () {
            $aliasLoader = AliasLoader::getInstance();
            $aliasLoader->alias('LaravelGettext', \pokovc\LaravelGettext\Facades\LaravelGettext::class);
        });

        $this->registerCommands();
    }

    /**
     * Register commands.
     */
    protected function registerCommands()
    {
        // Package commands
        $this->app->bind('pokovc::gettext.create', function ($app) {
            return new Commands\GettextCreate();
        });

        $this->app->bind('pokovc::gettext.update', function ($app) {
            return new Commands\GettextUpdate();
        });

        $this->commands([
            'pokovc::gettext.create',
            'pokovc::gettext.update',
        ]);
    }

    /**
     * Get the services.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'laravel-gettext'
        ];
    }
}
