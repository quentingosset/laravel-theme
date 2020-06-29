<?php

namespace Nwidart\Themes\Publishing;

use Nwidart\Themes\Support\Config\GenerateConfigReader;

class LangPublisher extends Publisher
{
    /**
     * Determine whether the result message will shown in the console.
     *
     * @var bool
     */
    protected $showMessage = false;

    /**
     * Get destination path.
     *
     * @return string
     */
    public function getDestinationPath()
    {
        $name = $this->theme->getLowerName();

        return base_path("resources/lang/{$name}");
    }

    /**
     * Get source path.
     *
     * @return string
     */
    public function getSourcePath()
    {
        return $this->getTheme()->getExtraPath(
            GenerateConfigReader::read('lang')->getPath()
        );
    }
}
