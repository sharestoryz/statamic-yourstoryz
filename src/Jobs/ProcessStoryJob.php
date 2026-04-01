<?php

namespace YourStoryz\StatamicYourStoryz\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;

class ProcessStoryJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private array $story,
        private array $data = []
    ) {}

    public function handle(): void
    {
        $existingEntry = Entry::query()
            ->where('collection', 'stories')
            ->where('reference_id', $this->story['id'])
            ->first();

        if ($existingEntry) {
            return;
        }

        $locale = Str::before(Site::default()->locale(), '_');

        $contents = collect($this->story['video']['contents'] ?? [])
            ->where('lang', $locale);

        $matched = $contents->firstWhere('type', 'news')
            ?? $contents->firstWhere('type', 'social');

        $title = $matched['title'] ?? $this->story['title'];
        $content = $matched['content'] ?? $this->story['description'];

        $title = Str::of($title);

        $data = array_merge([
            'title' => $title->toString(),
            'content' => $content,
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
