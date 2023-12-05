<?php

namespace EyadHamza\LaravelWebp\Traits;

use Illuminate\Support\Facades\Storage;

trait HandlePathConversion
{
    protected function buildWebpRelativePath(): string
    {
        return $this->getSlicedPathAtExtension() . '.webp';
    }

    protected function getSlicedPathAtExtension(): string
    {
        $imageParts = explode('.', $this->imageRelativePath);
        $sliced = array_slice($imageParts, 0, -1);
        if ($this->imageSettingsDto->height && $this->imageSettingsDto->width) {
            return implode('.', $sliced) . "_{$this->imageSettingsDto->width}x{$this->imageSettingsDto->height}";
        }

        return implode('.', $sliced);
    }

    public function toPhysicalPath($relativePath): string
    {
        return Storage::path($relativePath);
    }

    public function toRelativePath(string $fullPath): ?string
    {
        $url = explode('storage/', $fullPath)[1] ?? null;

        return 'public/' . $url;
    }

    public function toFullPath($relativePath): string
    {
        return asset('storage/' . explode('public/', $relativePath)[1] ?? null);
    }
}
