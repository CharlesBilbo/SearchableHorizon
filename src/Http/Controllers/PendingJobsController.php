<?php

namespace SearchableHorizon\Http\Controllers;

use Laravel\Horizon\Http\Controllers\PendingJobsController as BaseController;
use Illuminate\Http\Request;

class PendingJobsController extends BaseController
{
    public function index(Request $request)
    {
        $jobs = $this->jobs->getPending(
            $request->query('starting_at', -1),
            $request->query('search')
        )->map(function ($job) {
            $job->payload = json_decode($job->payload);
            return $job;
        })->values();

        return [
            'jobs' => $jobs,
            'total' => $this->jobs->countPending(),
        ];
    }
}