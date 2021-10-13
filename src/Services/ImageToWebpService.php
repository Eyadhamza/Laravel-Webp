<?php

namespace EyadHamza\LaravelWebp\Services;

use Exception;
use EyadHamza\LaravelWebp\Exceptions\ImageAlreadyExists;
use EyadHamza\LaravelWebp\Exceptions\NoImageGivenException;
use EyadHamza\LaravelWebp\Exceptions\NotImageException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ImageToWebpService
{
    public const  IMAGE_EXTENSIONS = ['PNG', 'jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd', 'webp'];

    private int $originalSize;
    private int $optimizedSize;
    private string $imageRelativePath;
    private string $webpRelativePath;
    private string $imagePhysicalPath;
    private string $webpPhysicalPath;
    private ?int $width;
    private ?int $height;
    private ?int $quality;

    public function __construct()
    {
        $this->width = config('webp.width');
        $this->height = config('webp.height');
        $this->quality = config('webp.quality');
        $this->webpRelativePath = '';
    }

    /**
     * @throws Exception
     */
    public function getOrCreate($imagePath, $width = null, $height = null): string
    {
        if (is_null($imagePath)) {
            throw new NoImageGivenException('No Image was given!');
        }

        if ($this->exists($imagePath, $width, $height)) {
            return $this->getWebpFullPath();
        }
        $this->save();

        return $this->getWebpFullPath();
    }

    /**
     * @throws Exception
     */
    public function exists($imagePath, $width = null, $height = null): bool
    {
        $this->setPath($imagePath, $width, $height);

        return Storage::exists($this->webpRelativePath);
    }

    /**
     * @throws Exception
     */
    public function save($quality = null): void
    {
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
    public function setPath($imagePath, $width = null, $height = null): void
    {
        if (is_null($imagePath)) {
            throw new NoImageGivenException('No Image was given!');
        }

        if ($this->isNotImage($imagePath)) {
            throw new NotImageException('This is not an image!');
        }
        $this->width = $width ?? $this->width;
        $this->height = $height ?? $this->height;

        $relativeImagePath = null;
        if (Str::contains($imagePath, 'http')) {
            $relativeImagePath = $this->toRelativePath($imagePath);
        }

        $this->imageRelativePath = $relativeImagePath ?? $imagePath;

        $this->buildNewRelativeWebpPath();

        $this->toPhysicalPath();
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

    public function getWebpRelativePath($imagePath): string
    {
        $this->buildNewRelativeWebpPath($imagePath);

        return $this->webpRelativePath;
    }

    public function getWebpFullPath($relativeImagePath = null): ?string
    {
        $this->buildNewRelativeWebpPath($relativeImagePath ?? $this->imageRelativePath);

        return $this->toFullPath($this->webpRelativePath);
    }

    private function buildNewRelativeWebpPath($imagePath = null)
    {
        // don't build relative path if it exists!
        if (Storage::exists($this->webpRelativePath)){
            return $this->webpRelativePath;
        }

        $this->webpRelativePath = $this
                ->getSlicedImageAtExtension($imagePath ?? null)[0]
                . "_{$this->width}x{$this->height}"
                . '.webp';

    }

    private function getSlicedImageAtExtension($imagePath = null): array
    {
        $imageParts = explode('.', $imagePath ?? $this->imageRelativePath);
        $sliced = array_slice($imageParts, 0, -1);

        return [implode('.', $sliced), end($imageParts)];
    }

    private function toPhysicalPath()
    {
        $this->webpPhysicalPath = Storage::path($this->webpRelativePath);
        $this->imagePhysicalPath = Storage::path($this->imageRelativePath);
    }

    public function toRelativePath(string $fullPath): ?string
    {
        $url = explode('storage/', $fullPath)[1] ?? null;

        return 'public/' . $url;
    }

    public function toFullPath(string $relativePath): ?string
    {
        return asset('storage/' . explode('public/', $relativePath)[1] ?? null);
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
}
