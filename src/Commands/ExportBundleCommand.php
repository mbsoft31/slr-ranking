<?php

namespace Mbsoft\SlrRanking\Commands;

use Illuminate\Console\Command;

class ExportBundleCommand extends Command
{
    protected $signature = 'slr:export {project} {--type=json} {--out=storage/app/slr}';

    protected $description = 'Command description';

    public function handle(): void
    {
        /* write CSV|MD|JSON|Mermaid files */
    }
}
