<?php

namespace Nwidart\Themes;

use Nwidart\Themes\Contracts\RepositoryInterface;
use Nwidart\Themes\Exceptions\InvalidActivatorClass;
use Nwidart\Themes\Support\Stub;

class LaravelThemesServiceProvider extends ThemesServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->registerNamespaces();
        $this->registerThemes();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerServices();
        $this->setupStubPath();
        $this->registerProviders();
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath()
    {
        Stub::setBasePath(__DIR__ . '/Commands/stubs');

        $this->app->booted(function ($app) {
            /** @var RepositoryInterface $themeRepository */
            $themeRepository = $app[RepositoryInterface::class];
            if ($themeRepository->config('stubs.enabled') === true) {
                Stub::setBasePath($themeRepository->config('stubs.path'));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {
        $this->app->singleton(Contracts\RepositoryInterface::class, function ($app) {
            $path = $app['config']->get('themes.paths.themes');

            return new Laravel\LaravelFileRepository($app, $path);
        });
        $this->app->singleton(Contracts\ActivatorInterface::class, function ($app) {
            $activator = $app['config']->get('themes.activator');
            $class = $app['config']->get('themes.activators.' . $activator)['class'];

            if ($class === null) {
                throw InvalidActivatorClass::missingConfig();
            }

            return new $class($app);
        });
        $this->app->alias(Contracts\RepositoryInterface::class, 'themes');
    }
}
