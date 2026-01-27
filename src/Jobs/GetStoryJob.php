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
        private array $data = []
    ) {
        //
    }

    public function handle(YourStoryz $yourstoryz): void
    {
        $response = $yourstoryz
            ->stories()
            ->get($this->story_id);

        $story = $response->json();

        Bus::chain([
            new ProcessStoryJob($story, $this->data),
            new ProcessMediaJob(
                reference_id: $story['id'],
                attribute: 'thumbnail',
                media_url: $story['video']['thumbnail_url']
            ),
            new ProcessMediaJob(
                reference_id: $story['id'],
                attribute: 'video',
                media_url: $story['video']['video_url']
            ),
        ])->dispatch();
    }
}
