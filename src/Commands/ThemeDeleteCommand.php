<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class ThemeDeleteCommand extends Command
{
    protected $name = 'theme:delete';
    protected $description = 'Delete a theme from the application';

    public function handle()
    {
        $this->laravel['themes']->delete($this->argument('theme'));

        $this->info("Theme {$this->argument('theme')} has been deleted.");
    }

    protected function getArguments()
    {
        return [
            ['theme', InputArgument::REQUIRED, 'The name of theme to delete.'],
        ];
    }
}
