<?php

namespace Mbsoft\SlrRanking\Commands;

use Illuminate\Console\Command;

class RecomputeScoresCommand extends Command
{
    protected $signature = 'slr:score {project} {--work=*}';

    protected $description = 'Command description';

    public function handle(): void
    {

    }
}
