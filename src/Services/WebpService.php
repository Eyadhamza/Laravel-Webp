<?php

namespace EyadHamza\LaravelWebp\Services;

use EyadHamza\LaravelWebp\DTOs\ImageSettingsDto;
use EyadHamza\LaravelWebp\Support\PathConversionSupport;
use EyadHamza\LaravelWebp\Validators\ImageConversionValidator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

class WebpService
{
    private ImageSettingsDto $imageSettingsDto;
    private ?string $imageRelativePath;
    private string $webpRelativePath;
    private bool $shouldResize;

    public function __construct(string $imageRelativePath = null, ImageSettingsDto $imageSettingsDto = null)
    {
        $this->imageRelativePath = PathConversionSupport::isFullPath($imageRelativePath) ? PathConversionSupport::toRelativePath($imageRelativePath) : $imageRelativePath;

        ImageConversionValidator::make($this->imageRelativePath)->validate();

        $this->imageSettingsDto = $imageSettingsDto ?? new ImageSettingsDto(
            config('webp.width'),
            config('webp.height'),
            config('webp.quality'),
        );

        $this->shouldResize = $this->imageSettingsDto->width && $this->imageSettingsDto->height;

        $this->webpRelativePath = PathConversionSupport::buildWebpRelativePath($this->imageRelativePath);
    }

    public static function make(string $imagePath = null, ImageSettingsDto $imageSettingsDto = null): self
    {
        return new self($imagePath, $imageSettingsDto);
    }

    public function save($quality = null): string
    {
        if ($this->exists()) {
            return $this->webpRelativePath;
        }

        $this->webpRelativePath = PathConversionSupport::appendWidthAndHeightToImageName($this->webpRelativePath, $this->imageSettingsDto->width, $this->imageSettingsDto->height);

        $webpPhysicalPath = PathConversionSupport::toPhysicalPath($this->webpRelativePath);
        $imagePhysicalPath = PathConversionSupport::toPhysicalPath($this->imageRelativePath);

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
}
