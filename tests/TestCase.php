<?php

namespace Pi\LaravelWebp\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as Orchestra;
use Pi\LaravelWebp\LaravelWebpServiceProvider;
use Pi\LaravelWebp\Tests\TestSupport\migrations\CreateTestImages;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Pi\LaravelWebp\Tests\TestSupport\factories\\'.class_basename($modelName).'Factory'
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
        (new CreateTestImages)->up();
    }
    public function getTestDirectory(): string
    {
        return __DIR__.'/TestSupport'.'/testimages';
    }
    public function getTestImageRelativePath(): string
    {
        return 'public/test.jpg';
    }

    public function prepareTestImage()
    {
        if (! Storage::exists('public/test.jpg')){
            Storage::copy('public/test.temp.jpg', 'public/test.jpg');
        }

    }

    public function refreshAndClean()
    {
        if (! Storage::exists('public/test.jpg')){
            Storage::copy('public/test.temp.jpg', 'public/test.jpg');
        }
        if (Storage::exists('public/test_x.webp')){
            Storage::delete('public/test_x.webp');
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
