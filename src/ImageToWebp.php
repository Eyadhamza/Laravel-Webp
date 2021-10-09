<?php

namespace Pi\LaravelWebp;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Pi\LaravelWebp\Services\ImageToWebpService
 * @method static void getOrCreate($imagePath, $width = null, $height = null)
 * @method static bool exists($imagePath, $width = null, $height = null)
 * @method static void saveAsWebp($quality = 70)
 * @method static void overwriteAsWebp($quality = 70)
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
