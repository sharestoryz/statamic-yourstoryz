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

    public function __construct(private array $data)
    {
        //
    }

    public function handle(): void
    {
        $title = Str::of($this->data['title']);

        $entry = Entry::make()
            ->collection('stories')
            ->slug($title->slug())
            ->date(new Carbon($this->data['created_at']))
            ->data([
                'title' => $title->toString(),
                'content' => $this->data['description'],
                'author' => $this->data['author']['name'],
                'reference_id' => $this->data['id'],
            ]);

        $entry->save();
    }
}
