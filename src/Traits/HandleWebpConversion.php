<?php

namespace Pi\LaravelWebp\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Pi\LaravelWebp\ImageToWebp;


trait HandleWebpConversion
{
    protected $imageService;
    protected $overwrite = false;


    // use Facades! instead of constuctors!
    protected function convertImageInDatabase(): string
    {

        $webpExtension = ImageToWebp::getWebpRelativePath($this->getImageField());
        $this->setImageField($webpExtension);
        $this->save();

        return $webpExtension;
    }

    public function saveImageAsWebp(string $imagePath = null, $width = null, $height = null)
    {

        try {
            ImageToWebp::setPath($imagePath ?? $this->getImageField());

            ImageToWebp::saveAsWebp();

            $this->convertImageInDatabase();

            Log::info(ImageToWebp::printInfo());

        } catch (\Exception $e) {
            Log::info($e);
        }

    }

    public function overwriteImageAsWebp(string $imagePath = null, $width = null, $height = null)
    {
        ImageToWebp::setPath($imagePath ?? $this->getImageField());

        ImageToWebp::overwriteAsWebp(70, $width, $height);

        $this->convertImageInDatabase();

    }

    public function getThumbnailImageAttribute($imagePath = null)
    {
        return ImageToWebp::getOrCreate($imagePath ?? $this->getImageField(),70,70);
    }

    public function resizeImage($width = 400,$height = 200, $imagePath = null)
    {
        return ImageToWebp::getOrCreate($imagePath ?? $this->getImageField(),$width,$height);
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
