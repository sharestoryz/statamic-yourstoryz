<?php

namespace YourStoryz\StatamicYourStoryz\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \YourStoryz\StatamicYourStoryz\StatamicYourStoryz
 */
class StatamicYourStoryz extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \YourStoryz\StatamicYourStoryz\StatamicYourStoryz::class;
    }
}
