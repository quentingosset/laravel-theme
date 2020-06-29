<?php

namespace Nwidart\Themes;

use Illuminate\Support\ServiceProvider;
use Nwidart\Themes\Providers\BootstrapServiceProvider;
use Nwidart\Themes\Providers\ConsoleServiceProvider;
use Nwidart\Themes\Providers\ContractsServiceProvider;
use Nwidart\Themes\Providers\ThemeServiceProvider;

abstract class ThemesServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
    }

    /**
     * Register all themes.
     */
    public function register()
    {
    }

    /**
     * Register all themes.
     */
    protected function registerThemes()
    {
        $this->app->register(BootstrapServiceProvider::class);
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($configPath, 'themes');
        $this->publishes([
            $configPath => config_path('themes.php'),
        ], 'config');
    }

    /**
     * Register the service provider.
     */
    abstract protected function registerServices();

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Contracts\RepositoryInterface::class, 'themes'];
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(ContractsServiceProvider::class);
        $this->app->register(ThemeServiceProvider::class);
    }
}
