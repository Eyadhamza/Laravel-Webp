<?php

namespace EyadHamza\LaravelWebp\Traits;

use EyadHamza\LaravelWebp\ImageToWebp;
use EyadHamza\LaravelWebp\Services\ImageToWebpService;
use Illuminate\Support\Facades\Log;

trait HandleWebpConversion
{
    protected ImageToWebpService $imageService;
    protected bool $overwrite = false;

    protected function convertImageInDatabase(): string
    {
        $webpExtension = ImageToWebp::getWebpRelativePath($this->getImageField());
        $this->setImageField($webpExtension);
        $this->save();

        return $webpExtension;
    }

    public function saveImageAsWebp(string $imagePath = null, $width = null, $height = null)
    {
        ImageToWebp::setPath($imagePath ?? $this->getImageField());

        ImageToWebp::save();

        $this->convertImageInDatabase();

        Log::info(ImageToWebp::printInfo());
    }

    public function overwriteImageAsWebp(string $imagePath = null, $width = null, $height = null)
    {
        ImageToWebp::setPath($imagePath ?? $this->getImageField());

        ImageToWebp::overwrite();

        $this->convertImageInDatabase();

        Log::info(ImageToWebp::printInfo());
    }

    public function resizeImage($width = 400, $height = 200, $imagePath = null): string
    {
        return ImageToWebp::getOrCreate($imagePath ?? $this->getImageField(), $width, $height);
    }

    protected function getImageField()
    {
        return data_get($this, $this->imageField);
    }

    protected function setImageField($value)
    {
        return data_set($this, $this->imageField, $value);
    }
}
