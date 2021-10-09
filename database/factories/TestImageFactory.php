<?php

namespace Pi\LaravelWebp\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Pi\LaravelWebp\Models\TestImage;

class TestImageFactory extends Factory
{
    protected $model = TestImage::class;

    public function definition(): array
    {
        return [
            'image' => ''
        ];
    }
}
