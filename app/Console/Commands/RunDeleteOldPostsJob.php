<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DeleteOldPosts;

class RunDeleteOldPostsJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:run-delete-old-posts-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description Delete old posts';


    public function handle()
    {
        DeleteOldPosts::dispatch();
        $this->info('Job to delete old soft-deleted posts has been dispatched.');
    }
}
