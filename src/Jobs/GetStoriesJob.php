<?php

namespace YourStoryz\StatamicYourStoryz\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Statamic\Facades\Entry;
use Statamic\Facades\YAML;
use YourStoryz\PhpSdk\YourStoryz;

class GetStoriesJob implements ShouldQueue
{
    use Queueable;

    private ?string $storiable_type;

    private ?int $storiable_id;

    public function __construct()
    {
        $data = YAML::file(base_path('content/yourstoryz.yaml'))->parse();

        $this->storiable_type = $data['storiable_type'] ?? null;
        $this->storiable_id = $data['storiable_id'] ?? null;
    }

    public function handle(YourStoryz $yourstoryz): void
    {
        if (empty($this->storiable_type) || empty($this->storiable_id)) {
            return;
        }

        $response = match ($this->storiable_type) {
            'user' => $yourstoryz->users()->stories($this->storiable_id),
            'company' => $yourstoryz->companies()->stories($this->storiable_id),
            'department' => $yourstoryz->departments()->stories($this->storiable_id),
        };

        $response->collect()->each(function ($story) {

            if (Entry::query()
                ->where('collection', 'stories')
                ->where('reference_id', $story['id'])
                ->exists()) {
                return;
            }

            GetStoryJob::dispatch($story['id']);
        });
    }
}
