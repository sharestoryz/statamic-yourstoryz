<?php

namespace YourStoryz\StatamicYourStoryz\Jobs;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Statamic\Facades\Asset;
use Statamic\Facades\Entry;
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
        // Process Story Job
        // Process Media Job // Video
        // Process Media Job // Thumbnail

        // Log::info($response->json());

        // $title = Str::of($data->title);

        // $temporaryFile = tempnam(sys_get_temp_dir(), 'statamic-yourstoryz');

        // Http::sink($temporaryFile)->get($data->video->thumbnail_url);

        // $path = Storage::disk('assets')->putFile('thumbnail', new File($temporaryFile));

        // $asset = Asset::make()
        //     ->container('assets')
        //     ->path($path);
        // $asset->save();

        // $entry = Entry::make()
        //     ->collection('stories')
        //     ->slug($title->slug())
        //     ->date(new Carbon($data->created_at))
        //     ->data([
        //         'title' => $title->toString(),
        //         'content' => $data->description,
        //         'author' => $data->author->name,
        //         'reference_id' => $data->id
        //     ]);

        // $entry->save();
    }
}
