<?php

namespace Mbsoft\SlrRanking\Commands;

use Illuminate\Console\Command;

class UploadSjrCommand extends Command
{
    protected $signature = 'upload:sjr';

    protected $description = 'Command description';

    public function handle(): void
    {
        /* read CSV -> lookups_sjr */
    }
}
