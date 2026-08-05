<?php

use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use YourStoryz\StatamicYourStoryz\Jobs\ProcessStoryJob;

function story(array $overrides = []): array
{
    return array_merge([
        'id' => 1065,
        'title' => 'A story title',
        'description' => 'A story description',
        'video' => [
            'id' => 1544,
            'contents' => [],
        ],
        'author' => null,
        'created_at' => '2025-03-30T21:18:49.000000Z',
    ], $overrides);
}

function fakeEntryFacades(): Closure
{
    $query = Mockery::mock();
    $query->shouldReceive('where')->andReturnSelf();
    $query->shouldReceive('first')->andReturnNull();

    $site = Mockery::mock();
    $site->shouldReceive('locale')->andReturn('nl_NL');

    $sites = Mockery::mock();
    $sites->shouldReceive('default')->andReturn($site);

    Site::swap($sites);

    $savedData = [];

    $entry = Mockery::mock();
    $entry->shouldReceive('collection', 'slug', 'date', 'published')->andReturnSelf();
    $entry->shouldReceive('data')->andReturnUsing(function (array $data) use (&$savedData, $entry) {
        $savedData = $data;

        return $entry;
    });
    $entry->shouldReceive('save')->andReturnTrue();

    $entries = Mockery::mock();
    $entries->shouldReceive('query')->andReturn($query);
    $entries->shouldReceive('make')->andReturn($entry);

    Entry::swap($entries);

    return function () use (&$savedData): array {
        return $savedData;
    };
}

it('stores a null author when the story has none', function () {
    $savedData = fakeEntryFacades();

    (new ProcessStoryJob(story()))->handle();

    expect($savedData())
        ->toHaveKey('author')
        ->and($savedData()['author'])->toBeNull()
        ->and($savedData()['reference_id'])->toBe(1065);
});

it('stores the author name when the story has one', function () {
    $savedData = fakeEntryFacades();

    (new ProcessStoryJob(story(['author' => ['name' => 'Jesse']])))->handle();

    expect($savedData()['author'])->toBe('Jesse');
});
