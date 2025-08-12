<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use App\Jobs\DownloadPostsJob;
use Illuminate\Http\Request;

class PostDownloadController extends Controller
{
    public function download(Request $request)
    {
        //the job may fail as well 
        //i want to to have a total of 2 retries
        // You can specify the number of retries in the job itself
        DownloadPostsJob::dispatch();

        return response()->json([
            'message' => 'Download job queued successfully. Check storage after job runs.'
        ]);
    }
}
