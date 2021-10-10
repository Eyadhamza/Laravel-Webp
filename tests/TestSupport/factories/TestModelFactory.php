<?php

namespace Pi\LaravelWebp\Tests\TestSupport\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Pi\LaravelWebp\Tests\TestSupport\Models\TestModel;

class TestModelFactory extends Factory
{
    protected $model = TestModel::class;

    public function definition(): array
    {
        return [
            'image' => ''
        ];
    }
}
