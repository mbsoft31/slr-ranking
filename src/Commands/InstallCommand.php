<?php

namespace Mbsoft\SlrRanking\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'slr:install';

    public function handle(): void
    {
        $this->callSilent('vendor:publish', ['--tag' => 'slr-ranking-config']);
        $this->callSilent('vendor:publish', ['--tag' => 'slr-ranking-migrations']);
        $this->info('SLR Ranking installed. Run: php artisan migrate');
    }
}
