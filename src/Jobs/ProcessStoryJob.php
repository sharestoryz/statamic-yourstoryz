<?php

namespace YourStoryz\StatamicYourStoryz\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;

class ProcessStoryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private array $story,
        private array $data = []
    ) {
        //
    }

    public function handle(): void
    {
        $title = Str::of($this->story['title']);

        $data = array_merge([
            'title' => $title->toString(),
            'content' => $this->story['description'],
            'author' => $this->story['author']['name'],
            'reference_id' => $this->story['id'],
        ], $this->data);

        $entry = Entry::make()
            ->collection('stories')
            ->slug($title->slug())
            ->date(new Carbon($this->story['created_at']))
            ->published(false)
            ->data($data);

        $entry->save();
    }
}
