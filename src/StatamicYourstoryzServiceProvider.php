<?php

namespace YourStoryz\StatamicYourstoryz;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use YourStoryz\StatamicYourstoryz\Commands\StatamicYourstoryzCommand;

class StatamicYourstoryzServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('statamic-yourstoryz')
            ->hasConfigFile()
            ->hasViews();
    }
}
