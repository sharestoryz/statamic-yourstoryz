<?php

namespace YourStoryz\StatamicYourStoryz\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;
use Statamic\Facades\Term;
use Statamic\Facades\YAML;
use YourStoryz\PhpSdk\YourStoryz;

class GetStoriesJob implements ShouldQueue
{
    use Queueable;

    private ?string $storiable_type;

    private ?int $storiable_id;

    private bool $include_departments;

    public function __construct()
    {
        $data = YAML::file(base_path('content/yourstoryz.yaml'))->parse();

        $this->storiable_type = $data['storiable_type'] ?? null;
        $this->storiable_id = $data['storiable_id'] ?? null;
        $this->include_departments = $data['include_departments'] ?? false;
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
            default => [],
        };

        $response->collect()
            ->filter(fn ($story) => filled($story['video']))
            ->filter(fn (array $story) => ! Entry::query()
                ->where('collection', 'stories')
                ->where('reference_id', $story['id'])
                ->exists())
            ->each(fn (array $story) => GetStoryJob::dispatch($story['id']));

        if ($this->storiable_type === 'company' && $this->include_departments) {
            $departments = $yourstoryz
                ->companies()
                ->departments($this->storiable_id)
                ->collect();

            $departments->each(function ($department) use ($yourstoryz) {
                $stories = $yourstoryz->departments()
                    ->stories($department['id'])
                    ->collect()
                    ->filter(fn ($story) => filled($story['video']));

                if ($stories->isEmpty()) {
                    return;
                }

                $category_slug = Str::slug($department['name']);

                Term::make()
                    ->taxonomy('categories')
                    ->slug($category_slug)
                    ->dataForLocale('default', [
                        'title' => $department['name'],
                    ])
                    ->save();

                $stories
                    ->filter(fn (array $story) => ! Entry::query()
                        ->where('collection', 'stories')
                        ->where('reference_id', $story['id'])
                        ->exists())
                    ->each(fn (array $story) => GetStoryJob::dispatch($story['id'], ['categories' => [$category_slug]]));
            });
        }
    }
}
