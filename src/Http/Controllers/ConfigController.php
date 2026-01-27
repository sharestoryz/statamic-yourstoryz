<?php

namespace YourStoryz\StatamicYourStoryz\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Statamic\Facades\Blueprint;
use Statamic\Facades\File;
use Statamic\Facades\YAML;

class ConfigController extends Controller
{
    protected array $defaults = [
        'storiable_type' => null,
        'storiable_id' => null,
        'include_departments' => null,
    ];

    public function edit()
    {
        $data = YAML::file(base_path('content/yourstoryz.yaml'))->parse() ?: $this->defaults;

        $config['storiable_type'] = $data['storiable_type'];
        $config[$data['storiable_type'].'_id'] = $data['storiable_id'];
        $config['include_departments'] = $data['include_departments'];

        $blueprint = $this->getBlueprint();

        $fields = $blueprint
            ->fields()
            ->addValues($config)
            ->preProcess();

        return view('statamic-yourstoryz::settings.edit', [
            'blueprint' => $blueprint->toPublishArray(),
            'values' => $fields->values(),
            'meta' => $fields->meta(),
        ]);
    }

    public function update(Request $request)
    {
        $blueprint = $this->getBlueprint();

        $fields = $blueprint->fields()->addValues($request->all());

        $fields->validate();

        $values = $fields->process()->values();

        $config['storiable_type'] = $values['storiable_type'];
        $config['storiable_id'] = match ($values['storiable_type']) {
            'company' => $values['company_id'],
            'department' => $values['department_id'],
            default => null,
        };
        $config['include_departments'] = $values['include_departments'];

        File::put(base_path('content/yourstoryz.yaml'), YAML::dump($config));
    }

    private function getBlueprint()
    {
        return Blueprint::find('statamic-yourstoryz::config');
    }
}
