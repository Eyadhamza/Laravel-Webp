<?php

namespace EyadHamza\LaravelWebp\DTOs;

class ImageSettingsDto
{
    public int $originalSize;
    public int $optimizedSize;

    public function __construct(
        public readonly ?int $width,
        public readonly ?int $height,
        public readonly ?int $quality,
    ) {
    }
}
