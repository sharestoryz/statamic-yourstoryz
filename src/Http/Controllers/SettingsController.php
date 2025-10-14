<?php

namespace YourStoryz\StatamicYourStoryz\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Blueprint;
use Statamic\Facades\YAML;
use Statamic\Fields\Blueprint as BlueprintContract;
use Statamic\Http\Controllers\CP\CpController;
use Statamic\Facades\Addon;

class SettingsController extends CpController
{
    public function edit()
    {
        $values = [];

        $blueprint = $this->getBlueprint();

        $fields = $blueprint->fields()->addValues($values)->preProcess();

        return view('yourstoryz::settings.edit', [
            'blueprint' => $blueprint->toPublishArray(),
            'values' => $fields->values(),
            'meta' => $fields->meta(),
        ]);
    }

    public function update(Request $request)
    {
        $fields = $this->getBlueprint()->fields()->addValues($request->all());

        $fields->validate();

        $values = $fields->process()->values()->all();

        // dd(Addon::make('vendor-name/addon-name')->getConfig('api_token'););

        return response()->json(['message' => __('Settings updated')]);
    }

    private function getBlueprint(): BlueprintContract
    {
        return Blueprint::make()->setContents(YAML::file(__DIR__.'/../../../resources/blueprints/config.yaml')->parse());
    }
}
