<?php

namespace Pi\LaravelWebp;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pi\LaravelWebp\Services\ImageToWebpService
 * @method static void getOrCreate
 */

class ImageToWebp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'imageToWebp';
    }
}
