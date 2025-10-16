<?php

namespace YourStoryz\StatamicYourStoryz;

use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $viewNamespace = 'yourstoryz';

    public function bootAddon() {}
}
