<?php

namespace EyadHamza\LaravelWebp\Services;

use EyadHamza\LaravelWebp\DTOs\ImageSettingsDto;
use EyadHamza\LaravelWebp\Traits\HandlePathConversion;
use EyadHamza\LaravelWebp\Validators\ImageConversionValidator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class WebpService
{
    use HandlePathConversion;


    private ImageSettingsDto $imageSettingsDto;
    private ImageConversionValidator $validator;
    private ?string $imageRelativePath;
    private string $webpRelativePath;
    private string $imageFullPath;
    private string $webpFullPath;
    private bool $shouldResize;

    public function __construct(string $imageRelativePath = null, ImageSettingsDto $imageSettingsDto = null)
    {
        $this->imageRelativePath = $this->isFullPath($imageRelativePath) ? $this->toRelativePath($imageRelativePath) : $imageRelativePath;

        $this->validator = ImageConversionValidator::make($this->imageRelativePath)->validate();

        $this->imageSettingsDto = $imageSettingsDto ?? new ImageSettingsDto(
            config('webp.width'),
            config('webp.height'),
            config('webp.quality'),
        );

        $this->shouldResize = $this->imageSettingsDto->width && $this->imageSettingsDto->height;

        $this->webpRelativePath = $this->buildWebpRelativePath();
    }

    public static function make(string $imagePath = null, ImageSettingsDto $imageSettingsDto = null): self
    {
        return new self($imagePath, $imageSettingsDto);
    }

    public function save($quality = null): string
    {
        if (Storage::exists($this->webpRelativePath)) {
            return $this->webpRelativePath;
        }

        $webpPhysicalPath = $this->toPhysicalPath($this->webpRelativePath);
        $imagePhysicalPath = $this->toPhysicalPath($this->imageRelativePath);

        $image = Image::make($imagePhysicalPath);

        if ($this->shouldResize) {
            $image->resize($this->imageSettingsDto->width, $this->imageSettingsDto->height);
        }

        $image->save($webpPhysicalPath, $quality ?? $this->imageSettingsDto->quality, 'webp');

        return $this->webpRelativePath;
    }

    public function overwrite($quality = null): string
    {
        $this->save($quality ?? $this->imageSettingsDto->quality);
        $this->deleteOld();

        return $this->webpRelativePath;
    }

    public function exists(): bool
    {
        return Storage::exists($this->webpRelativePath);
    }

    public function deleteOld(): void
    {
        Storage::delete($this->imageRelativePath);
    }

    public function getWebpRelativePath(): string
    {
        return $this->webpRelativePath;
    }

    public function getWebpFullPath(): ?string
    {
        return $this->webpFullPath;
    }

    private function isFullPath(string $imagePath = null): bool
    {
        return Str::startsWith($imagePath, 'http');
    }
}
