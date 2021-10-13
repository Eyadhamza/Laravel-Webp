<?php

namespace EyadHamza\LaravelWebp\Tests;

use EyadHamza\LaravelWebp\LaravelWebpServiceProvider;
use EyadHamza\LaravelWebp\Tests\TestSupport\migrations\CreateTestImages;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'EyadHamza\LaravelWebp\Tests\TestSupport\factories\\'.class_basename($modelName).'Factory'
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


        $migration = include_once __DIR__.'/TestSupport/migrations/٢٠٢١_١٠_٠٩_٢٣١٢٢٢_create_test_images_table.php.stub';
        (new CreateTestImages())->up();
    }

    public function getTestDirectory(): string
    {
        return __DIR__.'/TestSupport'.'/testimages';
    }

    public function getTestImageRelativePath(): string
    {
        return 'public/test.jpg';
    }

    public function getSecondTestImageRelativePath(): string
    {
        return 'public/test2.jpg';
    }

    public function getTestImageWebpRelativePath(): string
    {
        return 'public/test.webp';
    }

    public function getSecondTestImageWebpRelativePath(): string
    {
        return 'public/test2.webp';
    }

    public function getTempImageRelativePath(): string
    {
        return 'public/test.temp';
    }

    public function refreshAndClean()
    {
        if (! Storage::exists($this->getTestImageRelativePath())) {
            Storage::copy($this->getTempImageRelativePath(), $this->getTestImageRelativePath());
        }
        if (! Storage::exists($this->getSecondTestImageRelativePath())) {
            Storage::copy($this->getTempImageRelativePath(), $this->getSecondTestImageRelativePath());
        }
        if ($this->getTestImageWebpRelativePath()) {
            Storage::delete($this->getTestImageWebpRelativePath());
        }
        if ($this->getSecondTestImageWebpRelativePath()) {
            Storage::delete($this->getSecondTestImageWebpRelativePath());
        }
    }

    public function symLink()
    {
        App::make('files')->link(
            $this->getTestDirectory(),
            Storage::path('public')
        );
    }
}
