<?php

namespace EyadHamza\LaravelWebp\Tests\TestSupport\factories;

use EyadHamza\LaravelWebp\Tests\TestSupport\Models\TestModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class TestModelFactory extends Factory
{
    protected $model = TestModel::class;

    public function definition(): array
    {
        return [
            'image' => '',
        ];
    }
}
