<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Theme;
use Symfony\Component\Console\Input\InputArgument;

class DisableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable the specified theme.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /**
         * check if user entred an argument
         */
        if ($this->argument('theme') === null) {
            $this->disableAll();
        }

        /** @var Theme $theme */
        $theme = $this->laravel['themes']->findOrFail($this->argument('theme'));

        if ($theme->isEnabled()) {
            $theme->disable();

            $this->info("Theme [{$theme}] disabled successful.");
        } else {
            $this->comment("Theme [{$theme}] has already disabled.");
        }
    }

    /**
     * disableAll
     *
     * @return void
     */
    public function disableAll()
    {
        /** @var Themes $themes */
        $themes = $this->laravel['themes']->all();

        foreach ($themes as $theme) {
            if ($theme->isEnabled()) {
                $theme->disable();

                $this->info("Theme [{$theme}] disabled successful.");
            } else {
                $this->comment("Theme [{$theme}] has already disabled.");
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
