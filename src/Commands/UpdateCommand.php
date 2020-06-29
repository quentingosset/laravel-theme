<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Traits\ThemeCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class UpdateCommand extends Command
{
    use ThemeCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update dependencies for the specified theme or for all themes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('theme');

        if ($name) {
            $this->updateTheme($name);

            return;
        }

        /** @var \Nwidart\Themes\Theme $theme */
        foreach ($this->laravel['themes']->getOrdered() as $theme) {
            $this->updateTheme($theme->getName());
        }
    }

    protected function updateTheme($name)
    {
        $this->line('Running for theme: <info>' . $name . '</info>');

        $this->laravel['themes']->update($name);

        $this->info("Theme [{$name}] updated successfully.");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['theme', InputArgument::OPTIONAL, 'The name of theme will be updated.'],
        ];
    }
}
