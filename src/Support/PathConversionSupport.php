<?php

namespace EyadHamza\LaravelWebp\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PathConversionSupport
{
    public static function buildWebpRelativePath(string $imageRelativePath): string
    {
        $imageParts = explode('.', $imageRelativePath);

        $sliced = array_slice($imageParts, 0, -1);

        return implode('.', $sliced) . '.webp';
    }

    public static function toPhysicalPath($relativePath): string
    {
        return Storage::path($relativePath);
    }

    public static function toRelativePath(string $fullPath): ?string
    {
        $url = explode('storage/', $fullPath)[1] ?? null;

        return 'public/' . $url;
    }

    public function toFullPath($relativePath): string
    {
        return asset('storage/' . explode('public/', $relativePath)[1] ?? null);
    }

    public static function appendWidthAndHeightToImageName(string $webpRelativePath, float $width = null, float $height = null): string
    {
        if (!$width && !$height) {
            return $webpRelativePath;
        }

        return Str::replaceLast('.webp', "-{$width}x{$height}.webp", $webpRelativePath);
    }

    public static function isFullPath(string $imagePath = null): bool
    {
        return Str::startsWith($imagePath, 'http');
    }
}
