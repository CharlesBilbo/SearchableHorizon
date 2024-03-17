<?php

use Illuminate\Support\Facades\Route;
use SearchableHorizon\Http\Controller\PendingJobsController;
use SearchableHorizon\Http\Controllers\CompletedJobsController;

Route::prefix('api')->group(function () {
    Route::get('/jobs/pending', 'SearchableHorizon\Http\Controller\PendingJobsController@index')->name('horizon.pending-jobs.index');
    Route::get('/jobs/completed', 'SearchableHorizon\Http\Controllers\CompletedJobsController@index')->name('horizon.completed-jobs.index');
});