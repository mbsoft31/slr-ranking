<?php

namespace Mbsoft\SlrRanking\Commands;

use Illuminate\Console\Command;

class RunConnectorsCommand extends Command
{
    protected $signature = 'slr:run {project} {--openalex} {--crossref} {--arxiv} {--s2}';

    public function handle(): void
    {
        $projectId = $this->argument('project');
        // Dispatch jobs by flag; pass persisted search_strings
        // e.g., PullOpenAlexJob::dispatch($projectId, $q)->onQueue('pull');
    }
}
