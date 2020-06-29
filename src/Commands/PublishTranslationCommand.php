<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Theme;
use Nwidart\Themes\Publishing\LangPublisher;
use Symfony\Component\Console\Input\InputArgument;

class PublishTranslationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:publish-translation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a theme\'s translations to the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('theme')) {
            $this->publish($name);

            return;
        }

        $this->publishAll();
    }

    /**
     * Publish assets from all themes.
     */
    public function publishAll()
    {
        foreach ($this->laravel['themes']->allEnabled() as $theme) {
            $this->publish($theme);
        }
    }

    /**
     * Publish assets from the specified theme.
     *
     * @param string $name
     */
    public function publish($name)
    {
        if ($name instanceof Theme) {
            $theme = $name;
        } else {
            $theme = $this->laravel['themes']->findOrFail($name);
        }

        with(new LangPublisher($theme))
            ->setRepository($this->laravel['themes'])
            ->setConsole($this)
            ->publish();

        $this->line("<info>Published</info>: {$theme->getStudlyName()}");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['theme', InputArgument::OPTIONAL, 'The name of theme will be used.'],
        ];
    }
}
