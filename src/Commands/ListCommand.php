<?php

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Theme;
use Symfony\Component\Console\Input\InputOption;

class ListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show list of all themes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->table(['Name', 'Status', 'Order', 'Path'], $this->getRows());
    }

    /**
     * Get table rows.
     *
     * @return array
     */
    public function getRows()
    {
        $rows = [];

        /** @var Theme $theme */
        foreach ($this->getThemes() as $theme) {
            $rows[] = [
                $theme->getName(),
                $theme->isEnabled() ? 'Enabled' : 'Disabled',
                $theme->get('order'),
                $theme->getPath(),
            ];
        }

        return $rows;
    }

    public function getThemes()
    {
        switch ($this->option('only')) {
            case 'enabled':
                return $this->laravel['themes']->getByStatus(1);
                break;

            case 'disabled':
                return $this->laravel['themes']->getByStatus(0);
                break;

            case 'ordered':
                return $this->laravel['themes']->getOrdered($this->option('direction'));
                break;

            default:
                return $this->laravel['themes']->all();
                break;
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['only', 'o', InputOption::VALUE_OPTIONAL, 'Types of themes will be displayed.', null],
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'asc'],
        ];
    }
}
