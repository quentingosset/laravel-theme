<?php

namespace Nwidart\Themes\Traits;

trait MigrationLoaderTrait
{
    /**
     * Include all migrations files from the specified theme.
     *
     * @param string $theme
     */
    protected function loadMigrationFiles($theme)
    {
        $path = $this->laravel['themes']->getThemePath($theme) . $this->getMigrationGeneratorPath();

        $files = $this->laravel['files']->glob($path . '/*_*.php');

        foreach ($files as $file) {
            $this->laravel['files']->requireOnce($file);
        }
    }

    /**
     * Get migration generator path.
     *
     * @return string
     */
    protected function getMigrationGeneratorPath()
    {
        return $this->laravel['themes']->config('paths.generator.migration');
    }
}
