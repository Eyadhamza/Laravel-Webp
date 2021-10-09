<?php

namespace Pi\LaravelWebp\Services;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ImageToWebpService
{

    const  IMAGE_EXTENSIONS = ['PNG', 'jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd', 'webp'];

    private $originalSize;
    private $optimizedSize;
    private $imageRelativePath;
    private $webpRelativePath;
    private $imagePhysicalPath;
    private $webpPhysicalPath;
    private $width;
    private $height;

    public function getOrCreate($imagePath, $width = null, $height = null): string
    {
        if ($this->exists($imagePath, $width, $height)) {
            return $this->getWebpFullPath();
        }
        $this->saveAsWebp();
        return $this->getWebpFullPath();
    }

    public function exists($imagePath, $width = null, $height = null): bool
    {
        $this->setPath($imagePath, $width, $height);

        return Storage::exists($this->webpRelativePath);

    }

    public function saveAsWebp($quality = 70): void
    {
        $this->originalSize();

        if ($this->width && $this->height) {
            Image::make($this->imagePhysicalPath)
                ->resize($this->width, $this->height)
                ->save($this->webpPhysicalPath, $quality, 'webp');
        } else {
            Image::make($this->imagePhysicalPath)
                ->save($this->webpPhysicalPath, $quality, 'webp');
        }
        clearstatcache();
        $this->optimizedSize();
    }

    public function overwriteAsWebp($quality = 70): void
    {
        $this->saveAsWebp($quality);
        $this->deleteOld();
    }


    public function setPath($imagePath, $width = null, $height = null) : void
    {
        if ($this->isNotImage($imagePath)) {
            throw new \Exception('This is not an image!');
        }

        $this->width = $width ?? null;
        $this->height = $height ?? null;

        $relativeImagePath = null;
        if (\Str::contains($imagePath, 'http')) {
            $relativeImagePath = $this->toRelativePath($imagePath);
        }
        $this->imageRelativePath = $relativeImagePath ?? $imagePath;
        $this->buildNewRelativeWebpPath();
        $this->toPhysicalPath();
    }


    public function deleteOld()
    {
        if (Storage::exists($this->webpRelativePath)) {
            Storage::delete($this->imageRelativePath);
        }else{
            throw new Exception("trying to delete the original image while the new image doesn't exist!");
        }
    }


    private function getSlicedImageAtExtension($imagePath = null): array
    {
        $imageParts = explode('.', $imagePath ?? $this->imageRelativePath);
        $sliced = array_slice($imageParts, 0, -1);
        return [implode('.', $sliced), end($imageParts)];

    }

    public function getWebpRelativePath($imagePath): string
    {
        $this->buildNewRelativeWebpPath($imagePath);
        return $this->webpRelativePath;
    }

    public function getWebpFullPath(): ?string
    {
        $this->buildNewRelativeWebpPath($this->imageRelativePath);

        return $this->toFullPath($this->webpRelativePath);
    }

    private function buildNewRelativeWebpPath($imagePath = null)
    {
        $this->webpRelativePath = $this->getSlicedImageAtExtension($imagePath ?? null)[0] . "_{$this->width}x{$this->height}" . '.webp';
    }

    private function toPhysicalPath()
    {

        $this->webpPhysicalPath = Storage::path($this->webpRelativePath);
        $this->imagePhysicalPath = Storage::path($this->imageRelativePath);
    }

    private function toRelativePath(string $fullPath): ?string
    {
        $url = explode('storage/', $fullPath)[1] ?? null;

        return 'public/' . $url;
    }

    private function toFullPath(string $relativePath): ?string
    {
        return asset('storage/' . explode('public/', $relativePath)[1] ?? null);
    }

    private function isImage($file): bool
    {
        $filePathParts = explode('.', $file);
        return in_array(
            end($filePathParts),
            self::IMAGE_EXTENSIONS);
    }

    private function isNotImage($imagePath): bool
    {
        return !$this->isImage($imagePath);
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

    public function printInfo(): string
    {
        return ' Image: ' .
            $this->imageRelativePath . ' Before: ' .
            number_format($this->originalSize / 1048576, 4) . ' after: ' .
            number_format($this->optimizedSize / 1048576, 4) . ' Percentage: ' .
            $this->sizeDiff();
    }
}
