<?php
namespace SearchableHorizon\Contracts;

use Laravel\Horizon\Contracts\JobRepository as BaseRepository;

interface JobRepository extends BaseRepository
{
    /**
     * Get a chunk of pending jobs.
     *
     * @param  string  $afterIndex
     * @return \Illuminate\Support\Collection
     */
    public function getPending($afterIndex = null, string|null $query = null): \Illuminate\Support\Collection;

    /**
     * Get a chunk of completed jobs.
     *
     * @param  string  $afterIndex
     * @return \Illuminate\Support\Collection
     */
    public function getCompleted($afterIndex = null, string|null $query = null): \Illuminate\Support\Collection;

}
