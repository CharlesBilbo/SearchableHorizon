<?php

namespace SearchabelHorizon\Http\Controllers;

use Laravel\Horizon\Http\Controllers\CompletedJobsController as BaseController;
use Illuminate\Http\Request;

class CompletedJobsController extends BaseController
{
    /**
     * Get all the completed jobs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function index(Request $request)
    {
        $jobs = $this->jobs->getCompleted(
            $request->query('starting_at', -1),
            $request->query('search')
        )->map(function ($job) {
            $job->payload = json_decode($job->payload);
            return $job;
        })->values();

        return [
            'jobs' => $jobs,
            'total' => $this->jobs->countCompleted(),
        ];
    }
}