<?php

use Illuminate\Support\Facades\Process;

class InstallSearchable extends \Illuminate\Console\Command
{
    public $signature = 'horizon:install-search';

    public $description = 'Install the dependencies required for installing the search function in horizon';

    public function handle()
    {
        $connection = \Illuminate\Support\Facades\Redis::connection('horizon');

        $modules = $connection->executeRaw(['MODULE', 'LIST']);

        $notInstalled = true;
        foreach ($modules as $module) {
            if ($module[2] === 'search') {
                $notInstalled = false;
            }
        }

        if($notInstalled) {
            $this->error("The Redis module 'Search' is not installed");
        }

        $this->info('Creating index for jobs in redis');

        $connection->executeRaw([
            'FT.CREATE',
            'jobs_index',
            'ON',
            'HASH',
            'PREFIX',
            '1',
            'laravel_horizon:',
            'SCHEMA',
            'payload',
            'JSON',
            'exception',
            'TEXT',
            'context',
            'TEXT',
            'queue',
            'TEXT',
            'name',
            'TEXT'
        ]);

        $this->info('Installing NPM dependencies to recompile mix');

        Process::path(base_path() . '/vendor/laravel/horizon')->run('npm i');



        Process::path(base_path() . '/vendor/laravel/horizon')->run('npm run development');

    }
}