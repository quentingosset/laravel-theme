<?php

namespace Nwidart\Themes\Contracts;

use Nwidart\Themes\Theme;

interface ActivatorInterface
{
    /**
     * Enables a theme
     *
     * @param Theme $theme
     */
    public function enable(Theme $theme): void;

    /**
     * Disables a theme
     *
     * @param Theme $theme
     */
    public function disable(Theme $theme): void;

    /**
     * Determine whether the given status same with a theme status.
     *
     * @param Theme $theme
     * @param bool $status
     *
     * @return bool
     */
    public function hasStatus(Theme $theme, bool $status): bool;

    /**
     * Set active state for a theme.
     *
     * @param Theme $theme
     * @param bool $active
     */
    public function setActive(Theme $theme, bool $active): void;

    /**
     * Sets a theme status by its name
     *
     * @param  string $name
     * @param  bool $active
     */
    public function setActiveByName(string $name, bool $active): void;

    /**
     * Sets a default theme by its name
     *
     * @param  string $name
     */
    public function setThemeDefault(string $name): void;

    /**
     * get a default theme
     *
     * @return string
     */
    public function getThemeDefault(): string;

    /**
     * Deletes a theme activation status
     *
     * @param  Theme $theme
     */
    public function delete(Theme $theme): void;

    /**
     * Deletes any theme activation statuses created by this class.
     */
    public function reset(): void;
}
