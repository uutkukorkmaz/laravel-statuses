<?php

namespace Uutkukorkmaz\LaravelStatuses;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Uutkukorkmaz\LaravelStatuses\Commands\StatusGenerateCommand;

class LaravelStatusesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-statuses')
            ->hasConfigFile()
            ->hasCommand(StatusGenerateCommand::class);
    }
}
