<?php

namespace YourStoryz\StatamicYourStoryz\Fieldtypes;

use Statamic\Fieldtypes\Relationship;
use YourStoryz\LaravelYourStoryz\Facades\YourStoryz;

class YourStoryzCompany extends Relationship
{
    protected $canCreate = false;

    public static function handle()
    {
        return 'yourstoryz_company';
    }

    public function getIndexItems($request): array
    {
        return YourStoryz::companies()
            ->all()
            ->collect()
            ->map(fn ($item) => [
                'id' => $item['id'], 'title' => $item['name'],
            ])
            ->toArray();
    }

    protected function toItemArray($id): array
    {
        if (blank($id)) {
            return [];
        }

        $item = YourStoryz::companies()->get($id)->json();

        return [
            'id' => $item['id'],
            'title' => $item['name'],
        ];
    }
}
