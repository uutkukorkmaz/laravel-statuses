<?php

namespace Uutkukorkmaz\LaravelStatuses\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Uutkukorkmaz\LaravelStatuses\LaravelStatuses
 */
class Status extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Uutkukorkmaz\LaravelStatuses\LaravelStatuses::class;
    }
}
