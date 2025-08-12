<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DownloadPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    //i want to have a total of 2 retries
    public $tries = 2;
    // You can specify the delay in seconds before the job is retried
    public $retryUntil = 45; // 45 seconds
    public function __construct()
    {
        // You can pass filters or user IDs if needed
    }
    
    public function handle(): void
    {
        // Get all posts with related user, tagged users, and category
        $posts = Post::with('user', 'taggedUsers', 'category')->get();

        // Create CSV content
        $csv = "ID,Title,Author,Category,Tagged Users,Created At\n";

        foreach ($posts as $post) {
            $taggedUsers = $post->taggedUsers->pluck('name')->implode('|');
            $csv .= sprintf(
                "%d,%s,%s,%s,%s,%s\n",
                $post->id,
                str_replace(',', ' ', $post->title),
                $post->user->name ?? 'N/A',
                $post->category->name ?? 'N/A',
                $taggedUsers ?: 'None',
                $post->created_at
            );
        }

        // Store file in storage/app/posts.csv
        Storage::put('posts.csv', $csv);
    }
}
