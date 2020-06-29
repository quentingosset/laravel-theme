<?php

namespace Nwidart\Themes\Commands;

use Nwidart\Themes\Support\Config\GenerateConfigReader;
use Nwidart\Themes\Support\Stub;
use Nwidart\Themes\Traits\ThemeCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RouteProviderMakeCommand extends GeneratorCommand
{
    use ThemeCommandTrait;

    protected $argumentName = 'theme';

    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'theme:route-provider';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new route service provider for the specified theme.';

    /**
     * The command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['theme', InputArgument::OPTIONAL, 'The name of theme will be used.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the file already exists.'],
        ];
    }

    /**
     * Get template contents.
     *
     * @return string
     */
    protected function getTemplateContents()
    {
        $theme = $this->laravel['themes']->findOrFail($this->getThemeName());

        return (new Stub('/route-provider.stub', [
            'NAMESPACE'            => $this->getClassNamespace($theme),
            'CLASS'                => $this->getFileName(),
            'THEME_NAMESPACE'     => $this->laravel['themes']->config('namespace'),
            'THEME'               => $this->getThemeName(),
            'CONTROLLER_NAMESPACE' => $this->getControllerNameSpace(),
            'WEB_ROUTES_PATH'      => $this->getWebRoutesPath(),
            'API_ROUTES_PATH'      => $this->getApiRoutesPath(),
            'LOWER_NAME'           => $theme->getLowerName(),
        ]))->render();
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return 'RouteServiceProvider';
    }

    /**
     * Get the destination file path.
     *
     * @return string
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['themes']->getThemePath($this->getThemeName());

        $generatorPath = GenerateConfigReader::read('provider');

        return $path . $generatorPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return mixed
     */
    protected function getWebRoutesPath()
    {
        return '/' . $this->laravel['themes']->config('stubs.files.routes/web', 'Routes/web.php');
    }

    /**
     * @return mixed
     */
    protected function getApiRoutesPath()
    {
        return '/' . $this->laravel['themes']->config('stubs.files.routes/api', 'Routes/api.php');
    }

    public function getDefaultNamespace() : string
    {
        $theme = $this->laravel['themes'];

        return $theme->config('paths.generator.provider.namespace') ?: $theme->config('paths.generator.provider.path', 'Providers');
    }

    /**
     * @return string
     */
    private function getControllerNameSpace(): string
    {
        $theme = $this->laravel['themes'];

        return str_replace('/', '\\', $theme->config('paths.generator.controller.namespace') ?: $theme->config('paths.generator.controller.path', 'Controller'));
    }
}
