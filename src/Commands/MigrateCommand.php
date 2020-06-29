<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Migrations\Migrator;
use Nwidart\Themes\Theme;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate the migrations from the specified theme or from all themes.';

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

            return $this->migrate($theme);
        }

        foreach ($this->theme->getOrdered($this->option('direction')) as $theme) {
            $this->line('Running for theme: <info>' . $theme->getName() . '</info>');

            $this->migrate($theme);
        }
    }

    /**
     * Run the migration from the specified theme.
     *
     * @param Theme $theme
     */
    protected function migrate(Theme $theme)
    {
        $path = str_replace(base_path(), '', (new Migrator($theme, $this->getLaravel()))->getPath());

        if ($this->option('subpath')) {
            $path = $path . "/" . $this->option("subpath");
        }

        $this->call('migrate', [
            '--path' => $path,
            '--database' => $this->option('database'),
            '--pretend' => $this->option('pretend'),
            '--force' => $this->option('force'),
        ]);

        if ($this->option('seed')) {
            $this->call('theme:seed', ['theme' => $theme->getName()]);
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
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'asc'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['subpath', null, InputOption::VALUE_OPTIONAL, 'Indicate a subpath to run your migrations from'],
        ];
    }
}
