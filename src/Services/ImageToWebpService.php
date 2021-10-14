<?php

namespace EyadHamza\LaravelWebp\Services;

use Exception;
use EyadHamza\LaravelWebp\Exceptions\ImageAlreadyExists;
use EyadHamza\LaravelWebp\Exceptions\NoImageGivenException;
use EyadHamza\LaravelWebp\Exceptions\NotImageException;
use EyadHamza\LaravelWebp\Traits\HandlePathConversion;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ImageToWebpService
{
    use HandlePathConversion;
    public const  IMAGE_EXTENSIONS = ['PNG', 'jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd', 'webp'];

    private int $originalSize;
    private int $optimizedSize;
    private string $imageRelativePath;
    private string $webpRelativePath;
    private string $imagePhysicalPath;
    private string $webpPhysicalPath;
    private string $imageFullPath;
    private string $webpFullPath;
    private ?int $width;
    private ?int $height;
    private ?int $quality;

    public function __construct()
    {
        $this->width = config('webp.width');
        $this->height = config('webp.height');
        $this->quality = config('webp.quality');
    }

    /**
     * @throws \Throwable
     */
    public function make($imagePath, $width = null, $height = null): ImageToWebpService
    {
        throw_if(is_null($imagePath), new NoImageGivenException('No Image was given!'));

        throw_if($this->isNotImage($imagePath), new NotImageException('This is not an image!'));


        $this->width = $width ?? $this->width;
        $this->height = $height ?? $this->height;

        $relativeImagePath = null;
        if (Str::contains($imagePath, 'http')) {
            $relativeImagePath = $this->toRelativePath($imagePath);
        }
        $this->imageRelativePath = $relativeImagePath ?? $imagePath;


        $this->webpRelativePath = $this->buildNewRelativePath($this->imageRelativePath, $width, $height);
        $this->webpPhysicalPath = $this->toPhysicalPath($this->webpRelativePath);
        $this->imagePhysicalPath = $this->toPhysicalPath($this->imageRelativePath);
        $this->webpFullPath = $this->toFullPath($this->webpRelativePath);
        $this->imageFullPath = $this->toFullPath($this->imageRelativePath);

        return $this;
    }

    public function save($quality = null): string
    {
        if ($this->exists()){
            return $this->webpFullPath;
        }
        $this->originalSize();

        if ($this->width && $this->height) {
            Image::make($this->imagePhysicalPath)
                ->resize($this->width, $this->height)
                ->save($this->webpPhysicalPath, $quality ?? $this->quality, 'webp');
        } else {
            Image::make($this->imagePhysicalPath)
                ->save($this->webpPhysicalPath, $quality ?? $this->quality, 'webp');
        }
        clearstatcache();
        $this->optimizedSize();

        return $this->webpFullPath;
    }

    public function exists(): bool
    {
        return Storage::exists($this->webpRelativePath);
    }

    /**
     * @throws Exception
     */
    public function overwrite($quality = null): void
    {
        $this->save($quality ?? $this->quality);
        $this->deleteOld();
    }

    /**
     * @throws Exception
     */
    public function deleteOld()
    {
        if (Storage::exists($this->webpRelativePath)) {
            Storage::delete($this->imageRelativePath);
        } else {
            throw new Exception("trying to delete the original image while the new image doesn't exist!");
        }
    }

    public function getOldImageRelativePath(): string
    {
        return $this->imageRelativePath;
    }

    public function getWebpRelativePath(): string
    {
        return $this->webpRelativePath;
    }

    public function getWebpFullPath(): ?string
    {
        return $this->webpFullPath;
    }


    private function isImage($file): bool
    {
        $filePathParts = explode('.', $file);

        return in_array(
            end($filePathParts),
            self::IMAGE_EXTENSIONS
        );
    }

    private function isNotImage($imagePath): bool
    {
        return ! $this->isImage($imagePath);
    }

    public function printInfo(): string
    {
        return ' Image: ' .
            $this->imageRelativePath . ' Before: ' .
            number_format($this->originalSize / 1048576, 4) . ' after: ' .
            number_format($this->optimizedSize / 1048576, 4) . ' Percentage: ' .
            number_format($this->sizeDiff(), 2);
    }

    private function originalSize(): void
    {
        if (Storage::exists($this->imageRelativePath)) {
            $this->originalSize = Storage::size($this->imageRelativePath);
        } else {
            $this->originalSize = 0;
        }
    }

    private function optimizedSize()
    {
        $this->optimizedSize = Storage::size($this->webpRelativePath);
    }

    private function sizeDiff()
    {
        return (1 - $this->optimizedSize / $this->originalSize) * 100;
    }

    private function isWebp(): string
    {
        return strpos($this->imageRelativePath, '.webp') !== false;
    }
}
