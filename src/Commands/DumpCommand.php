<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class DumpCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump-autoload the specified theme or for all theme.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating optimized autoload themes.');

        if ($theme = $this->argument('theme')) {
            $this->dump($theme);
        } else {
            foreach ($this->laravel['themes']->all() as $theme) {
                $this->dump($theme->getStudlyName());
            }
        }
    }

    public function dump($theme)
    {
        $theme = $this->laravel['themes']->findOrFail($theme);

        $this->line("<comment>Running for theme</comment>: {$theme}");

        chdir($theme->getPath());

        passthru('composer dump -o -n -q');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['theme', InputArgument::OPTIONAL, 'Theme name.'],
        ];
    }
}
