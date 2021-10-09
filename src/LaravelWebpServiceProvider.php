<?php

namespace Pi\LaravelWebp;

use Pi\LaravelWebp\Commands\LaravelWebpCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelWebpServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-webp')
            ->hasConfigFile('laravel-webp')
            ->hasViews()
            ->hasCommand(LaravelWebpCommand::class);
    }
}
