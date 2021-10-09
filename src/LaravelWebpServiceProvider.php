<?php

namespace Pi\LaravelWebp;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Pi\LaravelWebp\Commands\LaravelWebpCommand;

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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-webp_table')
            ->hasCommand(LaravelWebpCommand::class);
    }
}
