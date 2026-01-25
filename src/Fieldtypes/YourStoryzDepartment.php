<?php

namespace YourStoryz\StatamicYourStoryz\Fieldtypes;

use Statamic\CP\Column;
use Statamic\Fieldtypes\Relationship;
use YourStoryz\LaravelYourStoryz\Facades\YourStoryz;

class YourStoryzDepartment extends Relationship
{
    protected $canCreate = false;

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
                'id' => $item['id'],
                'title' => $item['name'],
                'company' => $item['company']['name'],
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

    protected function getColumns()
    {
        return [
            Column::make('title'),
            Column::make('company'),
        ];
    }
}
