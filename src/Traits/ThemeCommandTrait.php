<?php

namespace Nwidart\Themes\Traits;

trait ThemeCommandTrait
{
    /**
     * Get the theme name.
     *
     * @return string
     */
    public function getThemeName()
    {
        $theme = $this->argument('theme') ?: app('themes')->getUsedNow();

        $theme = app('themes')->findOrFail($theme);

        return $theme->getStudlyName();
    }
}
