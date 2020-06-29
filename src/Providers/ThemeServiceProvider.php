<?php

namespace Nwidart\Themes\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * @var string $themeName
     */
    protected $themeName = '';

    /**
     * @var string $themeNameLower
     */
    protected $themeNameLower = '';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $defaultTheme = app('config')->get('themes.default');
        if($defaultTheme != null){
            $this->themeNameLower = $this->themeName = $defaultTheme;
            $this->registerTranslations();
            $this->registerConfig();
            $this->registerViews();
            $this->registerFactories();
            $this->loadMigrationsFrom(theme_path($this->themeName, 'Database/Migrations'));
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            theme_path($this->themeName, 'Config/config.php') => config_path($this->themeNameLower . '.php'),
        ], 'config');
        $this->mergeConfigFrom(
            theme_path($this->themeName, 'Config/config.php'), $this->themeNameLower
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/themes/' . $this->themeNameLower);

        $sourcePath = theme_path($this->themeName, 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->themeNameLower . '-theme-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), 'default');

    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/themes/' . $this->themeNameLower);

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $this->themeNameLower);
        } else {
            $this->loadTranslationsFrom(theme_path($this->themeName, 'Resources/lang'), $this->themeNameLower);
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production') && $this->app->runningInConsole()) {
            app(Factory::class)->load(theme_path($this->themeName, 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (\Config::get('view.paths') as $path) {
            if (is_dir($path . '/themes/' . $this->themeNameLower)) {
                $paths[] = $path . '/themes/' . $this->themeNameLower;
            }
        }
        return $paths;
    }
}
