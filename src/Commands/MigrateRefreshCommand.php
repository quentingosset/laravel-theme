<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Traits\ThemeCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateRefreshCommand extends Command
{
    use ThemeCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:migrate-refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback & re-migrate the themes migrations.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->call('theme:migrate-reset', [
            'theme' => $this->getThemeName(),
            '--database' => $this->option('database'),
            '--force' => $this->option('force'),
        ]);

        $this->call('theme:migrate', [
            'theme' => $this->getThemeName(),
            '--database' => $this->option('database'),
            '--force' => $this->option('force'),
        ]);

        if ($this->option('seed')) {
            $this->call('theme:seed', [
                'theme' => $this->getThemeName(),
            ]);
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
            ['theme', InputArgument::OPTIONAL, 'The name of theme will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
        ];
    }

    public function getThemeName()
    {
        $theme = $this->argument('theme');

        $theme = app('themes')->find($theme);

        if ($theme === null) {
            return $theme;
        }

        return $theme->getStudlyName();
    }
}
