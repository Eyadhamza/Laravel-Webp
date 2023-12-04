<?php

namespace EyadHamza\LaravelWebp\Tests;

use EyadHamza\LaravelWebp\LaravelWebpServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn(string $modelName) => 'EyadHamza\LaravelWebp\Tests\TestSupport\factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelWebpServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        $this->createTestTable();
    }

    private function createTestTable(): void
    {
        Schema::create('test_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }
}
