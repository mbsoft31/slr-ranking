<?php

namespace Mbsoft\SlrRanking;

use Mbsoft\SlrRanking\Commands\{ExportBundleCommand,
    InstallCommand,
    RecomputeScoresCommand,
    RunConnectorsCommand,
    UploadCoreCommand,
    UploadSjrCommand};
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
}
