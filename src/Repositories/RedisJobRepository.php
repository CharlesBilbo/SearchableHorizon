<?php

namespace SearchableHorizon\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Horizon\Repositories\RedisJobRepository as BaseRedisRepository;
use SearchableHorizon\Contracts\JobRepository;


class RedisJobRepository extends BaseRedisRepository implements JobRepository
{
    /**
     * Get a chunk of pending jobs.
     *
     * @param  string|null  $afterIndex
     * @return \Illuminate\Support\Collection
     */
    public function getPending($afterIndex = null, string|null $query = null): Collection
    {
        $type = 'pending_jobs';

        return isset($query)
            ? $this->getScopedJobsByQuery($type, $afterIndex, $query)
            : $this->getJobsByType($type, $afterIndex);
    }

    /**
     * Get a chunk of completed jobs.
     *
     * @param  string|null  $afterIndex
     * @return \Illuminate\Support\Collection
     */
    public function getCompleted($afterIndex = null, string|null $query = null): Collection
    {
        $type = 'completed_jobs';

        return isset($query)
            ? $this->getScopedJobsByQuery($type, $afterIndex, $query)
            : $this->getJobsByType($type, $afterIndex, $query);
    }

    /**
     * Scopes the jobs in redis based off the FT query
     * @param string $type
     * @param int $afterIndex
     * @param string|null $query
     * @return Collection
     */
    protected function getScopedJobsByQuery(string $type, int $afterIndex, string|null $query): Collection
    {
        $afterIndex = $afterIndex === null ? -1 : $afterIndex;
        $status = Str::before($type, '_jobs');
        $command = $this->connection()
            ->executeRaw([
                'FT.SEARCH',
                'jobs_index',
                "$query AND @status:$status",
                'RETURN',
                '1',
                '@id',
                'NOCONTENT',
                'LIMIT',
                $afterIndex,
                $afterIndex + 50
            ]);

        array_shift($command);

        $command = array_map(fn($item) => Str::after($item, 'laravel_horizon:'), $command);

        return $this->getJobs($command, $afterIndex + 1);
    }
}
