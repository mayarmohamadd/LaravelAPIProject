<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Post;
use Carbon\Carbon;


class DeleteOldPosts implements ShouldQueue
{

    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }


    public function handle()
    {
        $date = Carbon::now()->subDays(30);
        Post::onlyTrashed()->where('deleted_at', '<=', $date)->forceDelete();
    }
}
