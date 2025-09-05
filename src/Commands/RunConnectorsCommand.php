<?php

namespace Mbsoft\SlrRanking\Commands;

use Illuminate\Console\Command;
use Mbsoft\SlrRanking\Facades\SlrRanking;
use Mbsoft\SlrRanking\Models\Project;

class RunConnectorsCommand extends Command
{
    protected $signature = 'slr:run {project}
                        {--openalex}
                        {--crossref}
                        {--arxiv}
                        {--s2}';

    public function handle(): int
    {
        $projectId = (string) $this->argument('project');
        $proj = Project::findOrFail($projectId);
        $q = $proj->search_strings ?? [];

        if ($this->option('openalex')) {
            SlrRanking::openalex()->run($projectId, $q['openalex'] ?? []);
            $this->info('OpenAlex dispatched');
        }
        if ($this->option('crossref')) {
            SlrRanking::crossref()->run($projectId, $q['crossref'] ?? []);
            $this->info('Crossref dispatched');
        }
        if ($this->option('arxiv')) {
            SlrRanking::arxiv()->run($projectId, $q['arxiv'] ?? []);
            $this->info('arXiv dispatched');
        }
        if ($this->option('s2')) {
            SlrRanking::s2()->run($projectId, $q['s2'] ?? []);
            $this->info('S2 dispatched');
        }

        return self::SUCCESS;
    }
}
