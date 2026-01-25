<?php

namespace YourStoryz\StatamicYourStoryz\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Bus;
use YourStoryz\PhpSdk\YourStoryz;

class GetStoryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $story_id,
    ) {
        //
    }

    public function handle(YourStoryz $yourstoryz): void
    {
        $response = $yourstoryz
            ->stories()
            ->get($this->story_id);

        $data = $response->json();

        Bus::chain([
            new ProcessStoryJob($data),
            new ProcessMediaJob(
                reference_id: $data['id'],
                attribute: 'thumbnail',
                media_url: $data['video']['thumbnail_url']
            ),
            new ProcessMediaJob(
                reference_id: $data['id'],
                attribute: 'video',
                media_url: $data['video']['video_url']
            ),
        ])->dispatch();
    }
}
