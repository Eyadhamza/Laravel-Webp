<?php

namespace EyadHamza\LaravelWebp\Traits;

use Exception;
use EyadHamza\LaravelWebp\ImageToWebp;
use Illuminate\Support\Facades\Log;

trait HandleWebpConversion
{
    protected bool $overwrite = false;

    public function convertImageInDatabase($key, $fullPath)
    {
        $this->setImagesField($key, $fullPath);
        $this->save();
    }

    public function saveImageAsWebp(): void
    {
        foreach ($this->getImagesField() as $key => $fieldValue) {
            if ($this->$key) {
                try {
                    $image = ImageToWebp::make($this->$key);
                    $fullPath = $this->overwrite ? $image->overwrite() : $image->save();
                    Log::info(ImageToWebp::printInfo());
                    if ($this->$key != $fullPath){
                        $this->convertImageInDatabase($key, $fullPath);

                    }
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }
        }
    }

    public function overwriteImageAsWebp(): void
    {
        $this->overwrite = true;
        $this->saveImageAsWebp();
    }

    public function resize($imageAttribute, $width = 400, $height = 200): string
    {
        try {
            return ImageToWebp::make($this->$imageAttribute, $width, $height)
                ->save();
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
