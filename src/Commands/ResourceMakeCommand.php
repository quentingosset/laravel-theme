<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Support\Str;
use Nwidart\Themes\Support\Config\GenerateConfigReader;
use Nwidart\Themes\Support\Stub;
use Nwidart\Themes\Traits\ThemeCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ResourceMakeCommand extends GeneratorCommand
{
    use ThemeCommandTrait;

    protected $argumentName = 'name';
    protected $name = 'theme:make-resource';
    protected $description = 'Create a new resource class for the specified theme.';

    public function getDefaultNamespace() : string
    {
        $theme = $this->laravel['themes'];

        return $theme->config('paths.generator.resource.namespace') ?: $theme->config('paths.generator.resource.path', 'Transformers');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the resource class.'],
            ['theme', InputArgument::OPTIONAL, 'The name of theme will be used.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['collection', 'c', InputOption::VALUE_NONE, 'Create a resource collection.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $theme = $this->laravel['themes']->findOrFail($this->getThemeName());

        return (new Stub($this->getStubName(), [
            'NAMESPACE' => $this->getClassNamespace($theme),
            'CLASS'     => $this->getClass(),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['themes']->getThemePath($this->getThemeName());

        $resourcePath = GenerateConfigReader::read('resource');

        return $path . $resourcePath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * Determine if the command is generating a resource collection.
     *
     * @return bool
     */
    protected function collection() : bool
    {
        return $this->option('collection') ||
            Str::endsWith($this->argument('name'), 'Collection');
    }

    /**
     * @return string
     */
    protected function getStubName(): string
    {
        if ($this->collection()) {
            return '/resource-collection.stub';
        }

        return '/resource.stub';
    }
}
