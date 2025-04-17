<?php

namespace Itstudioat\Spa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Itstudioat\Spa\Spa
 */
class Spa extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Itstudioat\Spa\Spa::class;
    }
}
