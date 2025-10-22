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
                ->icon('shopping-cart');
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
