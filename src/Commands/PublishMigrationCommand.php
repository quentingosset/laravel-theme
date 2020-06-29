<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Migrations\Migrator;
use Nwidart\Themes\Publishing\MigrationPublisher;
use Symfony\Component\Console\Input\InputArgument;

class PublishMigrationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:publish-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a theme's migrations to the application";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($name = $this->argument('theme')) {
            $theme = $this->laravel['themes']->findOrFail($name);

            $this->publish($theme);

            return;
        }

        foreach ($this->laravel['themes']->allEnabled() as $theme) {
            $this->publish($theme);
        }
    }

    /**
     * Publish migration for the specified theme.
     *
     * @param \Nwidart\Themes\Theme $theme
     */
    public function publish($theme)
    {
        with(new MigrationPublisher(new Migrator($theme, $this->getLaravel())))
            ->setRepository($this->laravel['themes'])
            ->setConsole($this)
            ->publish();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['theme', InputArgument::OPTIONAL, 'The name of theme being used.'],
        ];
    }
}
