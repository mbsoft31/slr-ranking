<?php

namespace Mbsoft\SlrRanking;

use Illuminate\Support\Facades\Http;
use Mbsoft\SlrRanking\Commands\ExportBundleCommand;
use Mbsoft\SlrRanking\Commands\InstallCommand;
use Mbsoft\SlrRanking\Commands\RecomputeScoresCommand;
use Mbsoft\SlrRanking\Commands\RunConnectorsCommand;
use Mbsoft\SlrRanking\Commands\UploadCoreCommand;
use Mbsoft\SlrRanking\Commands\UploadSjrCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SlrRankingServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('slr-ranking')
            ->hasConfigFile('slr-ranking')
            ->hasMigration('create_slr_core_tables')
            ->hasCommands([
                InstallCommand::class,
                RunConnectorsCommand::class,
                RecomputeScoresCommand::class,
                ExportBundleCommand::class,
                UploadSjrCommand::class,
                UploadCoreCommand::class,
            ]);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // register slr Facade
        $this->app->singleton(SlrRanking::class, function () {
            return new SlrRanking;
        });

        // macro for Http client with SLR defaults
        Http::macro('slr', function () {
            $ua = 'mbsoft31/slr-ranking; contact='.config('slr-ranking.unpaywall_email');

            return Http::withHeaders(['User-Agent' => $ua])
                ->retry(3, 1000)
                ->timeout(config('slr-ranking.http.timeout'))
                ->connectTimeout(config('slr-ranking.http.connect_timeout'))
                ->withHeaders([
                    'User-Agent' => config('slr-ranking.http.user_agent'),
                ]);
        });
    }
}
