<?php

namespace EyadHamza\LaravelWebp\Casts;

use EyadHamza\LaravelWebp\DTOs\ImageSettingsDto;
use EyadHamza\LaravelWebp\Services\WebpService;
use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;

class ToWebpCast implements CastsInboundAttributes
{
    private ImageSettingsDto $imageSettingsDto;

    public function __construct(
        public readonly ?int $width = null,
        public readonly ?int $height = null,
        public readonly ?int $quality = null,
        public ?bool $overwrite = null,
    )
    {
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

        if ($imageService->exists()){
            return $imageService->getWebpRelativePath();
        }

        return $this->overwrite ? $imageService->overwrite() : $imageService->save();
    }
}
