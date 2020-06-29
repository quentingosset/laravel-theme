<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Support\Str;
use Nwidart\Themes\Theme;
use Nwidart\Themes\Support\Config\GenerateConfigReader;
use Nwidart\Themes\Support\Stub;
use Nwidart\Themes\Traits\ThemeCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ListenerMakeCommand extends GeneratorCommand
{
    use ThemeCommandTrait;

    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:make-listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event listener class for the specified theme';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the command.'],
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
            ['event', 'e', InputOption::VALUE_OPTIONAL, 'The event class being listened for.'],
            ['queued', null, InputOption::VALUE_NONE, 'Indicates the event listener should be queued.'],
        ];
    }

    protected function getTemplateContents()
    {
        $theme = $this->laravel['themes']->findOrFail($this->getThemeName());

        return (new Stub($this->getStubName(), [
            'NAMESPACE' => $this->getClassNamespace($theme),
            'EVENTNAME' => $this->getEventName($theme),
            'SHORTEVENTNAME' => $this->option('event'),
            'CLASS' => $this->getClass(),
        ]))->render();
    }

    public function getDefaultNamespace() : string
    {
        $theme = $this->laravel['themes'];

        return $theme->config('paths.generator.listener.namespace') ?: $theme->config('paths.generator.listener.path', 'Listeners');
    }

    protected function getEventName(Theme $theme)
    {
        $eventPath = GenerateConfigReader::read('event');

        return $this->getClassNamespace($theme) . "\\" . $eventPath->getPath() . "\\" . $this->option('event');
    }

    protected function getDestinationFilePath()
    {
        $path = $this->laravel['themes']->getThemePath($this->getThemeName());

        $listenerPath = GenerateConfigReader::read('listener');

        return $path . $listenerPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    protected function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * @return string
     */
    protected function getStubName(): string
    {
        if ($this->option('queued')) {
            if ($this->option('event')) {
                return '/listener-queued.stub';
            }

            return '/listener-queued-duck.stub';
        }

        if ($this->option('event')) {
            return '/listener.stub';
        }

        return '/listener-duck.stub';
    }
}
