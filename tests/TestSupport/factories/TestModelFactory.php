<?php

namespace EyadHamza\LaravelWebp\Tests\TestSupport\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use EyadHamza\LaravelWebp\Tests\TestSupport\Models\TestModel;

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
