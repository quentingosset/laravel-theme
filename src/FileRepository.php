<?php

namespace Nwidart\Themes;

use Countable;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Nwidart\Themes\Contracts\RepositoryInterface;
use Nwidart\Themes\Exceptions\InvalidAssetPath;
use Nwidart\Themes\Exceptions\ThemeNotFoundException;
use Nwidart\Themes\Process\Installer;
use Nwidart\Themes\Process\Updater;

abstract class FileRepository implements RepositoryInterface, Countable
{
    use Macroable;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected $app;

    /**
     * The theme path.
     *
     * @var string|null
     */
    protected $path;

    /**
     * The scanned paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * @var string
     */
    protected $stubPath;
    /**
     * @var UrlGenerator
     */
    private $url;
    /**
     * @var ConfigRepository
     */
    private $config;
    /**
     * @var Filesystem
     */
    private $files;
    /**
     * @var CacheManager
     */
    private $cache;

    /**
     * The constructor.
     * @param Container $app
     * @param string|null $path
     */
    public function __construct(Container $app, $path = null)
    {
        $this->app = $app;
        $this->path = $path;
        $this->url = $app['url'];
        $this->config = $app['config'];
        $this->files = $app['files'];
        $this->cache = $app['cache'];
    }

    /**
     * Add other theme location.
     *
     * @param string $path
     *
     * @return $this
     */
    public function addLocation($path)
    {
        $this->paths[] = $path;

        return $this;
    }

    /**
     * Get all additional paths.
     *
     * @return array
     */
    public function getPaths() : array
    {
        return $this->paths;
    }

    /**
     * Get scanned themes paths.
     *
     * @return array
     */
    public function getScanPaths() : array
    {
        $paths = $this->paths;

        $paths[] = $this->getPath();

        if ($this->config('scan.enabled')) {
            $paths = array_merge($paths, $this->config('scan.paths'));
        }

        $paths = array_map(function ($path) {
            return Str::endsWith($path, '/*') ? $path : Str::finish($path, '/*');
        }, $paths);

        return $paths;
    }

    /**
     * Creates a new Theme instance
     *
     * @param Container $app
     * @param string $args
     * @param string $path
     * @return \Nwidart\Themes\Theme
     */
    abstract protected function createTheme(...$args);

    /**
     * Get & scan all themes.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();

        $themes = [];

        foreach ($paths as $key => $path) {
            $manifests = $this->getFiles()->glob("{$path}/theme.json");

            is_array($manifests) || $manifests = [];

            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');

                $themes[$name] = $this->createTheme($this->app, $name, dirname($manifest));
            }
        }

        return $themes;
    }

    /**
     * Get all themes.
     *
     * @return array
     */
    public function all() : array
    {
        if (!$this->config('cache.enabled')) {
            return $this->scan();
        }

        return $this->formatCached($this->getCached());
    }

    /**
     * Format the cached data as array of themes.
     *
     * @param array $cached
     *
     * @return array
     */
    protected function formatCached($cached)
    {
        $themes = [];

        foreach ($cached as $name => $theme) {
            $path = $theme['path'];

            $themes[$name] = $this->createTheme($this->app, $name, $path);
        }

        return $themes;
    }

    /**
     * Get cached themes.
     *
     * @return array
     */
    public function getCached()
    {
        return $this->cache->remember($this->config('cache.key'), $this->config('cache.lifetime'), function () {
            return $this->toCollection()->toArray();
        });
    }

    /**
     * Get all themes as collection instance.
     *
     * @return Collection
     */
    public function toCollection() : Collection
    {
        return new Collection($this->scan());
    }

    /**
     * Get themes by status.
     *
     * @param $status
     *
     * @return array
     */
    public function getByStatus($status) : array
    {
        $themes = [];

        /** @var Theme $theme */
        foreach ($this->all() as $name => $theme) {
            if ($theme->isStatus($status)) {
                $themes[$name] = $theme;
            }
        }

        return $themes;
    }

    /**
     * Determine whether the given theme exist.
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name) : bool
    {
        return array_key_exists($name, $this->all());
    }

    /**
     * Get list of enabled themes.
     *
     * @return array
     */
    public function allEnabled() : array
    {
        return $this->getByStatus(true);
    }

    /**
     * Get list of disabled themes.
     *
     * @return array
     */
    public function allDisabled() : array
    {
        return $this->getByStatus(false);
    }

    /**
     * Get count from all themes.
     *
     * @return int
     */
    public function count() : int
    {
        return count($this->all());
    }

    /**
     * Get all ordered themes.
     *
     * @param string $direction
     *
     * @return array
     */
    public function getOrdered($direction = 'asc') : array
    {
        $themes = $this->allEnabled();

        uasort($themes, function (Theme $a, Theme $b) use ($direction) {
            if ($a->get('order') === $b->get('order')) {
                return 0;
            }

            if ($direction === 'desc') {
                return $a->get('order') < $b->get('order') ? 1 : -1;
            }

            return $a->get('order') > $b->get('order') ? 1 : -1;
        });

        return $themes;
    }

    /**
     * @inheritDoc
     */
    public function getPath() : string
    {
        return $this->path ?: $this->config('paths.themes', base_path('Themes'));
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        foreach ($this->getOrdered() as $theme) {
            $theme->register();
        }
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        foreach ($this->getOrdered() as $theme) {
            $theme->boot();
        }
    }

    /**
     * @inheritDoc
     */
    public function find(string $name)
    {
        foreach ($this->all() as $theme) {
            if ($theme->getLowerName() === strtolower($name)) {
                return $theme;
            }
        }

        return;
    }

    /**
     * @inheritDoc
     */
    public function findByAlias(string $alias)
    {
        foreach ($this->all() as $theme) {
            if ($theme->getAlias() === $alias) {
                return $theme;
            }
        }

        return;
    }

    /**
     * @inheritDoc
     */
    public function findRequirements($name): array
    {
        $requirements = [];

        $theme = $this->findOrFail($name);

        foreach ($theme->getRequires() as $requirementName) {
            $requirements[] = $this->findByAlias($requirementName);
        }

        return $requirements;
    }

    /**
     * Find a specific theme, if there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return Theme
     *
     * @throws ThemeNotFoundException
     */
    public function findOrFail(string $name)
    {
        $theme = $this->find($name);

        if ($theme !== null) {
            return $theme;
        }

        throw new ThemeNotFoundException("Theme [{$name}] does not exist!");
    }

    /**
     * Get all themes as laravel collection instance.
     *
     * @param $status
     *
     * @return Collection
     */
    public function collections($status = 1) : Collection
    {
        return new Collection($this->getByStatus($status));
    }

    /**
     * Get theme path for a specific theme.
     *
     * @param $theme
     *
     * @return string
     */
    public function getThemePath($theme)
    {
        try {
            return $this->findOrFail($theme)->getPath() . '/';
        } catch (ThemeNotFoundException $e) {
            return $this->getPath() . '/' . Str::studly($theme) . '/';
        }
    }

    /**
     * @inheritDoc
     */
    public function assetPath(string $theme) : string
    {
        return $this->config('paths.assets') . '/' . $theme;
    }

    /**
     * @inheritDoc
     */
    public function config(string $key, $default = null)
    {
        return $this->config->get('themes.' . $key, $default);
    }

    /**
     * Get storage path for theme used.
     *
     * @return string
     */
    public function getUsedStoragePath() : string
    {
        $directory = storage_path('app/themes');
        if ($this->getFiles()->exists($directory) === false) {
            $this->getFiles()->makeDirectory($directory, 0777, true);
        }

        $path = storage_path('app/themes/themes.used');
        if (!$this->getFiles()->exists($path)) {
            $this->getFiles()->put($path, '');
        }

        return $path;
    }

    /**
     * Set theme used for cli session.
     *
     * @param $name
     *
     * @throws ThemeNotFoundException
     */
    public function setUsed($name)
    {
        $theme = $this->findOrFail($name);

        $this->getFiles()->put($this->getUsedStoragePath(), $theme);
    }

    /**
     * Forget the theme used for cli session.
     */
    public function forgetUsed()
    {
        if ($this->getFiles()->exists($this->getUsedStoragePath())) {
            $this->getFiles()->delete($this->getUsedStoragePath());
        }
    }

    /**
     * Get theme used for cli session.
     * @return string
     * @throws \Nwidart\Themes\Exceptions\ThemeNotFoundException
     */
    public function getUsedNow() : string
    {
        return $this->findOrFail($this->getFiles()->get($this->getUsedStoragePath()));
    }

    /**
     * Get laravel filesystem instance.
     *
     * @return Filesystem
     */
    public function getFiles(): Filesystem
    {
        return $this->files;
    }

    /**
     * Get theme assets path.
     *
     * @return string
     */
    public function getAssetsPath() : string
    {
        return $this->config('paths.assets');
    }

    /**
     * Get asset url from a specific theme.
     * @param string $asset
     * @return string
     * @throws InvalidAssetPath
     */
    public function asset($asset) : string
    {
        if (Str::contains($asset, ':') === false) {
            throw InvalidAssetPath::missingThemeName($asset);
        }
        list($name, $url) = explode(':', $asset);

        $baseUrl = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $this->getAssetsPath());

        $url = $this->url->asset($baseUrl . "/{$name}/" . $url);

        return str_replace(['http://', 'https://'], '//', $url);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(string $name) : bool
    {
        return $this->findOrFail($name)->isEnabled();
    }

    /**
     * @inheritDoc
     */
    public function isDisabled(string $name) : bool
    {
        return !$this->isEnabled($name);
    }

    /**
     * @inheritDoc
     */
    public function setThemeDefault(string $name) : void
    {
        $this->findOrFail($name)->setThemeDefault();
    }

    /**
     * @inheritDoc
     */
    public function getThemeDefault() : string
    {
        return $this->config('default');
    }

    /**
     * Enabling a specific theme.
     * @param string $name
     * @return void
     * @throws \Nwidart\Themes\Exceptions\ThemeNotFoundException
     */
    public function enable($name)
    {
        $this->findOrFail($name)->enable();
    }

    /**
     * Disabling a specific theme.
     * @param string $name
     * @return void
     * @throws \Nwidart\Themes\Exceptions\ThemeNotFoundException
     */
    public function disable($name)
    {
        $this->findOrFail($name)->disable();
    }

    /**
     * @inheritDoc
     */
    public function delete(string $name) : bool
    {
        return $this->findOrFail($name)->delete();
    }

    /**
     * Update dependencies for the specified theme.
     *
     * @param string $theme
     */
    public function update($theme)
    {
        with(new Updater($this))->update($theme);
    }

    /**
     * Install the specified theme.
     *
     * @param string $name
     * @param string $version
     * @param string $type
     * @param bool   $subtree
     *
     * @return \Symfony\Component\Process\Process
     */
    public function install($name, $version = 'dev-master', $type = 'composer', $subtree = false)
    {
        $installer = new Installer($name, $version, $type, $subtree);

        return $installer->run();
    }

    /**
     * Get stub path.
     *
     * @return string|null
     */
    public function getStubPath()
    {
        if ($this->stubPath !== null) {
            return $this->stubPath;
        }

        if ($this->config('stubs.enabled') === true) {
            return $this->config('stubs.path');
        }

        return $this->stubPath;
    }

    /**
     * Set stub path.
     *
     * @param string $stubPath
     *
     * @return $this
     */
    public function setStubPath($stubPath)
    {
        $this->stubPath = $stubPath;

        return $this;
    }
}
