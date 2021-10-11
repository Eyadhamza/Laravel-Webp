<?php

namespace EyadHamza\LaravelWebp\Traits;

use EyadHamza\LaravelWebp\ImageToWebp;
use EyadHamza\LaravelWebp\Services\ImageToWebpService;
use Illuminate\Support\Facades\Log;

trait HandleWebpConversion
{
    protected ImageToWebpService $imageService;
    protected bool $overwrite = false;

    protected function convertImageInDatabase($key)
    {
        $webpRelativePath = ImageToWebp::getWebpRelativePath($this->$key);
        $this->setImagesField($key, $webpRelativePath);
        $this->save();
    }

    public function saveImageAsWebp($width = null, $height = null)
    {

        foreach ($this->getImagesField() as $key => $fieldValue) {

            ImageToWebp::setPath($this->$key);

            ImageToWebp::save();

            $this->convertImageInDatabase($key);

            Log::info(ImageToWebp::printInfo());

        }
    }

    public function overwriteImageAsWebp($width = null, $height = null)
    {
        foreach ($this->getImagesField() as $key => $fieldValue) {

            ImageToWebp::setPath($this->$key);

            ImageToWebp::overwrite();

            $this->convertImageInDatabase($key);

            Log::info(ImageToWebp::printInfo());

        }

    }

    // $this->resizeImage('image', 400, 400)
    public function resize($imageAttribute, $width = 400, $height = 200): string
    {
        return ImageToWebp::getOrCreate($this->$imageAttribute, $width, $height);
    }

    protected function getImagesField()
    {
        $imagesValues = [];
        foreach ($this->imageFields as $imageField) {
            $imagesValues[$imageField] = data_get($this, $imageField);
        }
        return $imagesValues;

    }

    protected function setImagesField($key, $value)
    {
        $this->setAttribute($key, $value);
    }
}
