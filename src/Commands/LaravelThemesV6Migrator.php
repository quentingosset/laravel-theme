<?php

declare(strict_types=1);

namespace Nwidart\Themes\Commands;

use Illuminate\Console\Command;
use Nwidart\Themes\Contracts\RepositoryInterface;
use Nwidart\Themes\Theme;

class LaravelThemesV6Migrator extends Command
{
    protected $name = 'theme:v6:migrate';
    protected $description = 'Migrate laravel-themes v5 themes statuses to v6.';

    public function handle()
    {
        $themeStatuses = [];
        /** @var RepositoryInterface $themes */
        $themes = $this->laravel['themes'];

        $themes = $themes->all();
        /** @var Theme $theme */
        foreach ($themes as $theme) {
            if ($theme->json()->get('active') === 1) {
                $theme->enable();
                $themeStatuses[] = [$theme->getName(), 'Enabled'];
            }
            if ($theme->json()->get('active') === 0) {
                $theme->disable();
                $themeStatuses[] = [$theme->getName(), 'Disabled'];
            }
        }
        $this->info('All themes have been migrated.');
        $this->table(['Theme name', 'Status'], $themeStatuses);
    }
}
