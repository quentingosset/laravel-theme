<?php

namespace Nwidart\Themes\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Themes\Commands\CommandMakeCommand;
use Nwidart\Themes\Commands\ControllerMakeCommand;
use Nwidart\Themes\Commands\DefaultThemeCommand;
use Nwidart\Themes\Commands\DisableCommand;
use Nwidart\Themes\Commands\DumpCommand;
use Nwidart\Themes\Commands\EnableCommand;
use Nwidart\Themes\Commands\EventMakeCommand;
use Nwidart\Themes\Commands\FactoryMakeCommand;
use Nwidart\Themes\Commands\InstallCommand;
use Nwidart\Themes\Commands\JobMakeCommand;
use Nwidart\Themes\Commands\LaravelThemesV6Migrator;
use Nwidart\Themes\Commands\ListCommand;
use Nwidart\Themes\Commands\ListenerMakeCommand;
use Nwidart\Themes\Commands\MailMakeCommand;
use Nwidart\Themes\Commands\MiddlewareMakeCommand;
use Nwidart\Themes\Commands\MigrateCommand;
use Nwidart\Themes\Commands\MigrateRefreshCommand;
use Nwidart\Themes\Commands\MigrateResetCommand;
use Nwidart\Themes\Commands\MigrateRollbackCommand;
use Nwidart\Themes\Commands\MigrateStatusCommand;
use Nwidart\Themes\Commands\MigrationMakeCommand;
use Nwidart\Themes\Commands\ModelMakeCommand;
use Nwidart\Themes\Commands\ThemeDeleteCommand;
use Nwidart\Themes\Commands\ThemeMakeCommand;
use Nwidart\Themes\Commands\NotificationMakeCommand;
use Nwidart\Themes\Commands\PolicyMakeCommand;
use Nwidart\Themes\Commands\ProviderMakeCommand;
use Nwidart\Themes\Commands\PublishCommand;
use Nwidart\Themes\Commands\PublishConfigurationCommand;
use Nwidart\Themes\Commands\PublishMigrationCommand;
use Nwidart\Themes\Commands\PublishTranslationCommand;
use Nwidart\Themes\Commands\RequestMakeCommand;
use Nwidart\Themes\Commands\ResourceMakeCommand;
use Nwidart\Themes\Commands\RouteProviderMakeCommand;
use Nwidart\Themes\Commands\RuleMakeCommand;
use Nwidart\Themes\Commands\SeedCommand;
use Nwidart\Themes\Commands\SeedMakeCommand;
use Nwidart\Themes\Commands\SetupCommand;
use Nwidart\Themes\Commands\TestMakeCommand;
use Nwidart\Themes\Commands\UnUseCommand;
use Nwidart\Themes\Commands\UpdateCommand;
use Nwidart\Themes\Commands\UseCommand;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     *
     * @var array
     */
    protected $commands = [
        CommandMakeCommand::class,
        ControllerMakeCommand::class,
        DisableCommand::class,
        DumpCommand::class,
        EnableCommand::class,
        EventMakeCommand::class,
        JobMakeCommand::class,
        ListenerMakeCommand::class,
        MailMakeCommand::class,
        MiddlewareMakeCommand::class,
        NotificationMakeCommand::class,
        ProviderMakeCommand::class,
        RouteProviderMakeCommand::class,
        InstallCommand::class,
        ListCommand::class,
        ThemeDeleteCommand::class,
        ThemeMakeCommand::class,
        FactoryMakeCommand::class,
        PolicyMakeCommand::class,
        RequestMakeCommand::class,
        RuleMakeCommand::class,
        MigrateCommand::class,
        MigrateRefreshCommand::class,
        MigrateResetCommand::class,
        MigrateRollbackCommand::class,
        MigrateStatusCommand::class,
        MigrationMakeCommand::class,
        ModelMakeCommand::class,
        PublishCommand::class,
        PublishConfigurationCommand::class,
        PublishMigrationCommand::class,
        PublishTranslationCommand::class,
        SeedCommand::class,
        SeedMakeCommand::class,
        SetupCommand::class,
        UnUseCommand::class,
        UpdateCommand::class,
        UseCommand::class,
        ResourceMakeCommand::class,
        TestMakeCommand::class,
        LaravelThemesV6Migrator::class,
        DefaultThemeCommand::class,
    ];

    /**
     * Register the commands.
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * @return array
     */
    public function provides()
    {
        $provides = $this->commands;

        return $provides;
    }
}
