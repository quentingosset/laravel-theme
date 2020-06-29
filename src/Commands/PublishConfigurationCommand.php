<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PublishConfigurationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:publish-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a theme\'s config files to the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($theme = $this->argument('theme')) {
            $this->publishConfiguration($theme);

            return;
        }

        foreach ($this->laravel['themes']->allEnabled() as $theme) {
            $this->publishConfiguration($theme->getName());
        }
    }

    /**
     * @param string $theme
     * @return string
     */
    private function getServiceProviderForTheme($theme)
    {
        $namespace = $this->laravel['config']->get('themes.namespace');
        $studlyName = Str::studly($theme);

        return "$namespace\\$studlyName\\Providers\\{$studlyName}ServiceProvider";
    }

    /**
     * @param string $theme
     */
    private function publishConfiguration($theme)
    {
        $this->call('vendor:publish', [
            '--provider' => $this->getServiceProviderForTheme($theme),
            '--force' => $this->option('force'),
            '--tag' => ['config'],
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
            ['theme', InputArgument::OPTIONAL, 'The name of theme being used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['--force', '-f', InputOption::VALUE_NONE, 'Force the publishing of config files'],
        ];
    }
}
