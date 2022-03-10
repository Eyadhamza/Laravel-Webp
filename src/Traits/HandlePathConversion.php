<?php

namespace EyadHamza\LaravelWebp\Traits;

use Illuminate\Support\Facades\Storage;

trait HandlePathConversion
{
    protected function buildNewRelativePath($relativePath, $width = null, $height = null): string
    {
        return $this->getSlicedPathAtExtension($relativePath, $width, $height) . '.webp';
    }

    protected function getSlicedPathAtExtension($path, $width, $height): string
    {
        $imageParts = explode('.', $path);
        $sliced = array_slice($imageParts, 0, -1);
        if ($height && $width) {
            return implode('.', $sliced) . "_{$width}x{$height}";
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
