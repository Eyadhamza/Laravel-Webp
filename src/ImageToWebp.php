<?php

namespace Pi\LaravelWebp;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pi\LaravelWebp\Services\ImageToWebpService
 * @method static string getOrCreate($imagePath, $width = null, $height = null)
 * @method static bool exists($imagePath, $width = null, $height = null)
 * @method static void save($quality = 70)
 * @method static void overwrite($quality = 70)
 * @method static void setPath($imagePath, $width = null, $height = null)
 * @method static void deleteOld()
 * @method static string getWebpRelativePath($imagePath)
 * @method static string getWebpFullPath()
 * @method static string printInfo()
 */

class ImageToWebp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'imageToWebp';
    }
}
