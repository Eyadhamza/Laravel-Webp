<?php

namespace Pi\LaravelWebp;


use Pi\LaravelWebp\Commands\AttachmentsOptimizeCommand;
use Pi\LaravelWebp\Commands\ConvertAssetImagesCommand;
use Pi\LaravelWebp\Commands\ConvertImagesToWebpCommand;
use Pi\LaravelWebp\Commands\OptimizeImagesCommand;
use Pi\LaravelWebp\Services\ImageToWebpService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelWebpServiceProvider extends PackageServiceProvider
{
    public function register()
    {
         parent::register();

        $this->app->bind('imageToWebp', function($app) {
            return new ImageToWebpService();
        });
    }

    public function configurePackage(Package $package): void
    {

        $package
            ->name('laravel-webp')
            ->hasConfigFile('laravel-webp')
            ->hasCommands([
                AttachmentsOptimizeCommand::class,
                ConvertAssetImagesCommand::class,
                ConvertImagesToWebpCommand::class,
                OptimizeImagesCommand::class
            ]);
    }
}
