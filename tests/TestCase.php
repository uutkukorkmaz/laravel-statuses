<?php

namespace Uutkukorkmaz\LaravelStatuses\Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as Orchestra;
use Uutkukorkmaz\LaravelStatuses\LaravelStatusesServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->beforeApplicationDestroyed(function () {
            File::cleanDirectory(app_path('Enums'));
            File::cleanDirectory(app_path('Models'));
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelStatusesServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
    }
}
