<?php

namespace YourStoryz\StatamicYourStoryz\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Statamic\Facades\Asset;
use Statamic\Facades\Entry;

class ProcessMediaJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private int $reference_id,
        private string $attribute,
        private string $media_url
    ) {
        //
    }

    public function handle(): void
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), 'statamic-yourstoryz');

        Http::sink($temporaryFile)
            ->get($this->media_url);

        $path = Storage::disk('assets')->putFileAs(
            Str::plural($this->attribute),
            new File($temporaryFile),
            Str::of($this->media_url)->basename()
        );

        $asset = Asset::make()
            ->container('assets')
            ->path($path);
        $asset->save();

        $entry = Entry::query()
            ->where('collection', 'stories')
            ->where('reference_id', $this->reference_id)
            ->first();

        $entry->set($this->attribute, [$path]);
        $entry->save();
    }
}
