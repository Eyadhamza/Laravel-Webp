<?php

namespace EyadHamza\LaravelWebp\Traits;

use Exception;
use EyadHamza\LaravelWebp\ImageToWebp;
use Illuminate\Support\Facades\Log;

trait HandleWebpConversion
{
    protected bool $overwrite = false;

    public function convertImageInDatabase($key)
    {
        ImageToWebp::setPath($this->$key);
        $this->setImagesField($key, ImageToWebp::getWebpFullPath());
        $this->save();
    }

    public function saveImageAsWebp(): void
    {
        foreach ($this->getImagesField() as $key => $fieldValue) {
            try {
                ImageToWebp::setPath($this->$key);

                ImageToWebp::save();

                $this->convertImageInDatabase($key);

                Log::info(ImageToWebp::printInfo());
            } catch (Exception $e) {
                Log::info($e->getMessage());
            }
        }
    }

    public function overwriteImageAsWebp(): void
    {
        foreach ($this->getImagesField() as $key => $fieldValue) {
            try {
                ImageToWebp::setPath($this->$key);

                ImageToWebp::overwrite();

                $this->convertImageInDatabase($key);

                Log::info(ImageToWebp::printInfo());
            } catch (Exception $e) {
                Log::alert($e->getMessage());
            }
        }
    }

    public function resize($imageAttribute, $width = 400, $height = 200): string
    {
        try {
            return ImageToWebp::getOrCreate($this->$imageAttribute, $width, $height);
        } catch (Exception $e) {
            Log::alert($e->getMessage());
        }

        return '';
    }

    public function getImagesField(): array
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
