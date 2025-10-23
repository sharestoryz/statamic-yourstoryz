<?php

namespace YourStoryz\StatamicYourStoryz;

use Illuminate\Console\Scheduling\Schedule;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Site;
use Statamic\Providers\AddonServiceProvider;
use YourStoryz\StatamicYourStoryz\Jobs\GetStoriesJob;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        \YourStoryz\StatamicYourStoryz\Fieldtypes\YourStoryzCompany::class,
        \YourStoryz\StatamicYourStoryz\Fieldtypes\YourStoryzDepartment::class,
    ];

    public function bootAddon(): void
    {
        $this
            ->createCollection()
            ->createBlueprint();

        Nav::extend(function ($nav) {
            $nav->content('YourStoryz')
                ->section('Tools')
                ->route('yourstoryz.edit')
                ->icon('<svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500"><path fill-rule="evenodd" clip-rule="evenodd" d="M288.535 34.977c28.703-19.214 69.459-9.528 86.585 19.214 5.709 9.21 8.722 20.167 8.722 31.283s-3.013 22.073-8.722 31.441c-2.537 4.288-5.709 8.258-9.039 11.752L326.118 171.7c-6.501 6.511-9.832 14.609-9.832 23.343s3.33 16.833 9.515 22.867l40.755 43.827c3.013 3.018 6.185 6.988 8.722 11.434 5.709 9.21 8.722 20.167 8.722 31.441 0 11.275-3.013 22.073-8.722 31.442-5.044 8.153-11.955 14.961-19.988 19.964-11.652 8.806-45.42 34.212-143.667 107.866C200.84 471.348 188.947 475 176.895 475c-21.249 0-41.39-11.434-52.173-29.695-5.709-9.369-8.722-20.167-8.722-31.441 0-11.275 3.013-22.073 8.88-31.442 2.537-4.287 5.551-8.257 9.039-11.591l39.963-43.035c6.501-6.51 9.832-14.608 9.832-23.342 0-8.733-3.331-16.832-9.515-23.025L133.49 237.81a62.14 62.14 0 0 1-1.785-1.882c-.06-.066-.119-.134-.178-.201a62.413 62.413 0 0 1-1.388-1.608l-.156-.188a60.796 60.796 0 0 1-10.471-18.536l-.124-.347a60.853 60.853 0 0 1-3.36-18.314c-.004-.117-.005-.234-.008-.351-.012-.498-.02-.998-.02-1.498 0-.501.008-1.002.02-1.501.003-.117.004-.234.008-.351a60.065 60.065 0 0 1 2.653-16.065c3.459-11.275 10.164-21.403 19.52-29.087 1.752-1.594 149.192-112.086 150.334-112.904Zm-76.828 262.344a62.74 62.74 0 0 1 .392 6.973c0 16.356-6.342 31.601-17.76 43.193l-39.962 43.033c-2.062 2.065-3.806 4.288-5.233 6.67-3.172 5.081-4.758 10.798-4.758 16.674 0 5.875 1.585 11.592 4.598 16.515 9.356 15.562 30.765 20.484 45.988 10.322 23.692-17.817 70.959-53.242 105.806-79.417a65.967 65.967 0 0 1-12.084-6.175c-31.98-24.001-57.158-42.9-76.987-57.788ZM176.895 162.49c-11.259 0-21.884 6.035-27.751 15.721a31.51 31.51 0 0 0-2.01 3.766 32.604 32.604 0 0 0-2.59 12.748c0 9.687 4.282 18.738 11.894 25.09 3.806 3.017 108.785 81.78 148.748 111.793 4.915 3.176 11.101 5.24 17.603 5.24 5.72 0 11.201-1.476 15.985-4.186 2.505-1.904 4.064-3.097 4.471-3.436 7.453-6.035 11.735-15.086 11.735-24.932 0-9.845-4.282-18.897-11.893-24.931-5.077-4.13-147.322-110.998-148.432-111.633-5.074-3.334-11.258-5.24-17.76-5.24ZM322.789 52.763c-6.502 0-12.687 1.905-18.237 5.558-.314.209-62.319 46.733-106.08 79.639a57.238 57.238 0 0 1 12.518 6.269c.66.495 39.073 29.283 77.004 57.746a61.826 61.826 0 0 1-.41-7.09c0-16.356 6.343-31.601 17.919-43.193l39.962-43.034c2.22-2.064 3.806-4.287 5.233-6.669 3.013-5.08 4.6-10.798 4.6-16.673 0-5.876-1.587-11.593-4.6-16.516-6.026-10.003-16.492-16.037-27.909-16.037Z" fill="currentColor"/></svg>');
        });
    }

    protected function schedule(Schedule $schedule): void
    {
        $schedule
            ->job(new GetStoriesJob)
            ->everyFifteenMinutes();
    }

    protected function createCollection(): self
    {
        if (Collection::handleExists('stories')) {
            return $this;
        }

        Collection::make('stories')
            ->title('Stories')
            ->routes('/stories/{slug}')
            ->dated(true)
            ->defaultPublishState('draft')
            ->sites([Site::default()->handle()])
            ->save();

        return $this;
    }

    protected function createBlueprint(): self
    {
        Blueprint::make('story')
            ->setNamespace('collections.stories')
            ->setContents([
                'title' => 'Story',
                'sections' => [
                    'main' => ['fields' => [
                        ['handle' => 'title', 'field' => ['type' => 'text']],
                        ['handle' => 'content', 'field' => ['type' => 'markdown']],
                        ['handle' => 'video', 'field' => ['type' => 'assets', 'container' => 'assets', 'max_items' => 1]],
                        ['handle' => 'thumbnail', 'field' => ['type' => 'assets', 'container' => 'assets', 'max_items' => 1]],
                        ['handle' => 'author', 'field' => ['type' => 'text']],
                        ['handle' => 'reference_id', 'field' => ['type' => 'text', 'visibility' => 'read_only', 'duplicate' => 'false']],
                    ]],
                ],
            ])
            ->save();

        return $this;
    }
}
