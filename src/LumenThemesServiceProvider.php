<?php

namespace Nwidart\Themes;

use Nwidart\Themes\Support\Stub;

class LumenThemesServiceProvider extends ThemesServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->setupStubPath();
    }

    /**
     * Register all themes.
     */
    public function register()
    {
        $this->registerNamespaces();
        $this->registerServices();
        $this->registerThemes();
        $this->registerProviders();
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath()
    {
        Stub::setBasePath(__DIR__ . '/Commands/stubs');

        if (app('themes')->config('stubs.enabled') === true) {
            Stub::setBasePath(app('themes')->config('stubs.path'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {
        $this->app->singleton(Contracts\RepositoryInterface::class, function ($app) {
            $path = $app['config']->get('themes.paths.themes');

            return new Lumen\LumenFileRepository($app, $path);
        });
        $this->app->singleton(Contracts\ActivatorInterface::class, function ($app) {
            $activator = $app['config']->get('themes.activator');
            $class = $app['config']->get('themes.activators.' . $activator)['class'];

            return new $class($app);
        });
        $this->app->alias(Contracts\RepositoryInterface::class, 'themes');
    }
}
