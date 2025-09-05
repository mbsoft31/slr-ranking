<?php

namespace Mbsoft\SlrRanking;

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
}
