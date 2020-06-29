<?php

namespace Nwidart\Themes\Process;

use Nwidart\Themes\Contracts\RepositoryInterface;
use Nwidart\Themes\Contracts\RunableInterface;

class Runner implements RunableInterface
{
    /**
     * The theme instance.
     * @var RepositoryInterface
     */
    protected $theme;

    public function __construct(RepositoryInterface $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Run the given command.
     *
     * @param string $command
     */
    public function run($command)
    {
        passthru($command);
    }
}
