<?php

namespace Pi\LaravelWebp;

use Pi\LaravelWebp\Commands\ToWepExtensionFieldCommand;
use Pi\LaravelWebp\Commands\AssetsToWebpCommand;
use Pi\LaravelWebp\Commands\ToWebpImageFieldCommand;
use Pi\LaravelWebp\Commands\DirectoryToWebpCommand;
use Pi\LaravelWebp\Services\ImageToWebpService;
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
            ->hasConfigFile('laravel-webp')
            ->hasCommands([
                ToWepExtensionFieldCommand::class,
                ToWebpImageFieldCommand::class,
                DirectoryToWebpCommand::class,
            ]);
    }
}
