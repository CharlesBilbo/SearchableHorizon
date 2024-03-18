<?php

namespace SearchableHorizon\Providers;

class HorizonSearchableProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            \SearchableHorizon\Contracts\JobRepository::class,
            \SearchableHorizon\Repositories\RedisJobRepository::class
        );

        $this->commands(
            InstallSearchable::class
        );
    }

    public function boot()
    {
    }
}