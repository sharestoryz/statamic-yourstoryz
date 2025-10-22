<?php

namespace YourStoryz\StatamicYourStoryz\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Statamic\Facades\Blueprint;
use Statamic\Facades\File;
use Statamic\Facades\YAML;

class ConfigController extends Controller
{
    public function edit()
    {
        $data = YAML::file(base_path('content/yourstoryz.yaml'))->parse();

        $blueprint = $this->getBlueprint();

        $fields = $blueprint
            ->fields()
            ->addValues($data)
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

        $data = $fields->process()->values()->toArray();

        File::put(base_path('content/yourstoryz.yaml'), YAML::dump($data));
    }

    private function getBlueprint()
    {
        return Blueprint::find('statamic-yourstoryz::config');
    }
}
