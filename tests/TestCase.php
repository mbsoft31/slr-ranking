<?php

namespace Mbsoft\SlrRanking\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Mbsoft\SlrRanking\SlrRankingServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Mbsoft\\SlrRanking\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        // Register your package provider so facades/macros/config load
        return [
            SlrRankingServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // run the migrations for the package
        foreach (File::allFiles(__DIR__.'/../database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
        }

    }
}
