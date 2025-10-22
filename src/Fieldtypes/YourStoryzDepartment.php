<?php

namespace YourStoryz\StatamicYourStoryz\Fieldtypes;

use Statamic\Fieldtypes\Relationship;
use YourStoryz\LaravelYourStoryz\Facades\YourStoryz;

class YourStoryzDepartment extends Relationship
{
    public static function handle()
    {
        return 'yourstoryz_department';
    }

    public function getIndexItems($request): array
    {
        return YourStoryz::departments()
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

        $item = YourStoryz::departments()->get($id)->json();

        return [
            'id' => $item['id'],
            'title' => $item['name'],
        ];
    }
}
