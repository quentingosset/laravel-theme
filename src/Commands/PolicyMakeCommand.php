<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Support\Str;
use Nwidart\Themes\Support\Config\GenerateConfigReader;
use Nwidart\Themes\Support\Stub;
use Nwidart\Themes\Traits\ThemeCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class PolicyMakeCommand extends GeneratorCommand
{
    use ThemeCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:make-policy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new policy class for the specified theme.';

    public function getDefaultNamespace() : string
    {
        $theme = $this->laravel['themes'];

        return $theme->config('paths.generator.policies.namespace') ?: $theme->config('paths.generator.policies.path', 'Policies');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the policy class.'],
            ['theme', InputArgument::OPTIONAL, 'The name of theme will be used.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $theme = $this->laravel['themes']->findOrFail($this->getThemeName());

        return (new Stub('/policy.plain.stub', [
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

        $policyPath = GenerateConfigReader::read('policies');

        return $path . $policyPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
}
