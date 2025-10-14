<?php

namespace YourStoryz\StatamicYourstoryz\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \YourStoryz\StatamicYourstoryz\StatamicYourstoryz
 */
class StatamicYourstoryz extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \YourStoryz\StatamicYourstoryz\StatamicYourstoryz::class;
    }
}
