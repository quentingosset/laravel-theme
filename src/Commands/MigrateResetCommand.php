<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Migrations\Migrator;
use Nwidart\Themes\Traits\MigrationLoaderTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateResetCommand extends Command
{
    use MigrationLoaderTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:migrate-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the themes migrations.';

    /**
     * @var \Nwidart\Themes\Contracts\RepositoryInterface
     */
    protected $theme;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->theme = $this->laravel['themes'];

        $name = $this->argument('theme');

        if (!empty($name)) {
            $this->reset($name);

            return;
        }

        foreach ($this->theme->getOrdered($this->option('direction')) as $theme) {
            $this->line('Running for theme: <info>' . $theme->getName() . '</info>');

            $this->reset($theme);
        }
    }

    /**
     * Rollback migration from the specified theme.
     *
     * @param $theme
     */
    public function reset($theme)
    {
        if (is_string($theme)) {
            $theme = $this->theme->findOrFail($theme);
        }

        $migrator = new Migrator($theme, $this->getLaravel());

        $database = $this->option('database');

        if (!empty($database)) {
            $migrator->setDatabase($database);
        }

        $migrated = $migrator->reset();

        if (count($migrated)) {
            foreach ($migrated as $migration) {
                $this->line("Rollback: <info>{$migration}</info>");
            }

            return;
        }

        $this->comment('Nothing to rollback.');
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
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'desc'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
        ];
    }
}
