<?php

namespace EyadHamza\LaravelWebp\Validators;

use EyadHamza\LaravelWebp\Exceptions\NoImageGivenException;
use EyadHamza\LaravelWebp\Exceptions\NotImageException;

class ImageConversionValidator
{
    public const IMAGE_EXTENSIONS = ['PNG', 'jpg', 'jpeg', 'gif', 'png', 'bmp', 'svg', 'svgz', 'cgm', 'djv', 'djvu', 'ico', 'ief', 'jpe', 'pbm', 'pgm', 'pnm', 'ppm', 'ras', 'rgb', 'tif', 'tiff', 'wbmp', 'xbm', 'xpm', 'xwd', 'webp'];


    public function __construct(
        private readonly ?string $imagePath,
    )
    {
    }

    public static function make(string $imagePath = null): static
    {
        return new static($imagePath);
    }

    /**
     * @throws NoImageGivenException
     * @throws NotImageException
     */
    public function validate(): self
    {
        return $this
            ->validateNotNull()
            ->validateIsImage();
    }

    /**
     * @throws NoImageGivenException
     */
    private function validateNotNull(): self
    {
        if (!$this->imagePath) {
            throw new NoImageGivenException('No Image was given!');
        }

        return $this;
    }

    private function validateIsImage(): self
    {
        if (!$this->isImage($this->imagePath)) {
            throw new NotImageException('This is not an image!');
        }

        return $this;
    }


    private function isImage($file): bool
    {
        $filePathParts = explode('.', $file);

        return in_array(
            end($filePathParts),
            self::IMAGE_EXTENSIONS
        );
    }

}
