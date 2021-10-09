<?php

namespace Pi\LaravelWebp;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pi\LaravelWebp\LaravelWebp
 */
class LaravelWebpFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-webp';
    }
}
