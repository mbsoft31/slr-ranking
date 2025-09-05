<?php

namespace Mbsoft\SlrRanking\Commands;

use Illuminate\Console\Command;

class UploadCoreCommand extends Command
{
    protected $signature = 'upload:core';

    protected $description = 'Command description';

    public function handle(): void
    {
        /* read CSV -> lookups_core */
    }
}
