<?php

namespace Nwidart\Themes\Providers;

use Illuminate\Support\ServiceProvider;
use Nwidart\Themes\Contracts\RepositoryInterface;
use Nwidart\Themes\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
