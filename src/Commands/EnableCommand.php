<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Theme;
use Symfony\Component\Console\Input\InputArgument;

class EnableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:enable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable the specified theme.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         * check if user entred an argument
         */
        if ($this->argument('theme') === null) {
            $this->enableAll();
        }

        /** @var Theme $theme */
        $theme = $this->laravel['themes']->findOrFail($this->argument('theme'));

        if ($theme->isDisabled()) {
            $theme->enable();

            $this->info("Theme [{$theme}] enabled successful.");
        } else {
            $this->comment("Theme [{$theme}] has already enabled.");
        }
    }

    /**
     * enableAll
     *
     * @return void
     */
    public function enableAll()
    {
        /** @var Themes $themes */
        $themes = $this->laravel['themes']->all();

        foreach ($themes as $theme) {
            if ($theme->isDisabled()) {
                $theme->enable();

                $this->info("Theme [{$theme}] enabled successful.");
            } else {
                $this->comment("Theme [{$theme}] has already enabled.");
            }
        }
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
