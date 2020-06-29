<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class UseCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:use';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use the specified theme.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $theme = Str::studly($this->argument('theme'));

        if (!$this->laravel['themes']->has($theme)) {
            $this->error("Theme [{$theme}] does not exists.");

            return;
        }

        $this->laravel['themes']->setUsed($theme);

        $this->info("Theme [{$theme}] used successfully.");
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['theme', InputArgument::REQUIRED, 'The name of theme will be used.'],
        ];
    }
}
