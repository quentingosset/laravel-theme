<?php

namespace Nwidart\Themes\Lumen;

use Nwidart\Themes\FileRepository;

class LumenFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createTheme(...$args)
    {
        return new Theme(...$args);
    }
}
