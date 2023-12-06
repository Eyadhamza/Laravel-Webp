<?php

namespace EyadHamza\LaravelWebp\Casts;

use EyadHamza\LaravelWebp\DTOs\ImageSettingsDto;
use EyadHamza\LaravelWebp\Services\WebpService;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Support\Str;

class ToWebpCast implements CastsInboundAttributes
{
    private ImageSettingsDto $imageSettingsDto;

    public function __construct(
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?int $quality = null,
        public ?bool         $overwrite = null,
    ) {
        $this->overwrite = $overwrite ?? config('webp.overwrite');
        $this->imageSettingsDto = new ImageSettingsDto(
            config('webp.width') ?? $width,
            config('webp.height') ?? $height,
            config('webp.quality') ?? $quality,
        );
    }

    public function set($model, string $key, mixed $value, array $attributes): string
    {
        $imageService = WebpService::make($value, $this->imageSettingsDto);

        if ($imageService->exists()) {
            return $imageService->getWebpRelativePath();
        }

        return $this->overwrite ? $imageService->overwrite() : $imageService->save();
    }

    public static function convertCastToDto(mixed $attribute): ImageSettingsDto
    {
        $cast = Str::after($attribute, ':');
        $cast = explode(',', $cast);

        return new ImageSettingsDto(
            width: $cast[0] ?? null,
            height: $cast[1] ?? null,
            quality: $cast[2] ?? null,
        );
    }
}
