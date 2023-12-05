<?php

namespace EyadHamza\LaravelWebp;

use EyadHamza\LaravelWebp\Commands\AllModelsToWebpCommand;
use EyadHamza\LaravelWebp\Commands\DirectoryToWebpCommand;
use EyadHamza\LaravelWebp\Commands\ToWebpImageFieldCommand;
use EyadHamza\LaravelWebp\Commands\ToWepExtensionFieldCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelWebpServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-webp')
            ->hasConfigFile('webp')
            ->hasCommands([
                ToWepExtensionFieldCommand::class,
                AllModelsToWebpCommand::class,
                DirectoryToWebpCommand::class,
                ToWebpImageFieldCommand::class,
            ]);
    }
}
