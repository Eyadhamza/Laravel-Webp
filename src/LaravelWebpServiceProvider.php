<?php

namespace EyadHamza\LaravelWebp;

use EyadHamza\LaravelWebp\Commands\DirectoryToWebpCommand;
use EyadHamza\LaravelWebp\Commands\AllModelsToWebpCommand;
use EyadHamza\LaravelWebp\Commands\ToWebpImageFieldCommand;
use EyadHamza\LaravelWebp\Commands\ToWepExtensionFieldCommand;
use EyadHamza\LaravelWebp\Services\ImageToWebpService;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelWebpServiceProvider extends PackageServiceProvider
{
    public function register()
    {
        parent::register();

        $this->app->bind('imageToWebp', function ($app) {
            return new ImageToWebpService();
        });
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-webp')
            ->hasConfigFile('webp')
            ->hasCommands([
                ToWepExtensionFieldCommand::class,
                AllModelsToWebpCommand::class,
                DirectoryToWebpCommand::class,
                ToWebpImageFieldCommand::class
            ]);
    }
}
