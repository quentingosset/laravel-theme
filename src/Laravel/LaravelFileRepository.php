<?php

namespace Nwidart\Themes\Laravel;

use Nwidart\Themes\FileRepository;

class LaravelFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createTheme(...$args)
    {
        return new Theme(...$args);
    }
}
