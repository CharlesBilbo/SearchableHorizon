<?php
namespace SearchableHorizon\Commands;

use Illuminate\Support\Facades\Process;
use Mockery\Exception;

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

        try {
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
        } catch (\RedisException $exception) {
            $this->error($exception->getMessage());
        }

        $this->info('Installing NPM dependencies to recompile mix');

        Process::path(base_path() . '/vendor/laravel/horizon')->run('npm i');

        $this->info('Copying Files into Horizon');

        $viewCommand = 'cp ../Views/index.vue ' . base_path('/vendor/laravel/horizon/resources/js/screens/recentJobs/index.vue');
        $routesCommand = 'cp ../routes/api.js ' . base_path('/vendor/laravel/horizon/routes/web.php');

        Process::path(__DIR__)->run($viewCommand);


        $this->info('Recompiling Mix');

        Process::path(base_path() . '/vendor/laravel/horizon')->run('npm run development');

        $this->info('Publishing compiled JS');

        Process::path(base_path())->run('php artisan hoirzon:publish --force');

        $this->info('SUCCESS');
    }
}