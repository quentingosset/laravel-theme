<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Migrations\Migrator;
use Nwidart\Themes\Theme;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateStatusCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:migrate-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Status for all theme migrations';

    /**
     * @var \Nwidart\Themes\Contracts\RepositoryInterface
     */
    protected $theme;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->theme = $this->laravel['themes'];

        $name = $this->argument('theme');

        if ($name) {
            $theme = $this->theme->findOrFail($name);

            return $this->migrateStatus($theme);
        }

        foreach ($this->theme->getOrdered($this->option('direction')) as $theme) {
            $this->line('Running for theme: <info>' . $theme->getName() . '</info>');
            $this->migrateStatus($theme);
        }
    }

    /**
     * Run the migration from the specified theme.
     *
     * @param Theme $theme
     */
    protected function migrateStatus(Theme $theme)
    {
        $path = str_replace(base_path(), '', (new Migrator($theme, $this->getLaravel()))->getPath());

        $this->call('migrate:status', [
            '--path' => $path,
            '--database' => $this->option('database'),
        ]);
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
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'asc'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
        ];
    }
}
