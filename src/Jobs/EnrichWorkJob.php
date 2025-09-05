<?php

namespace Mbsoft\SlrRanking\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mbsoft\SlrRanking\Services\EnrichmentService;

class EnrichWorkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $workId) {}

    public function handle(): void
    {
        $svc = app(EnrichmentService::class);
        $svc->unpaywall($this->workId);
        $svc->codeAndDataLinks($this->workId);
        $svc->citations($this->workId);
        ComputeScoresJob::dispatch($this->workId)->onQueue('score');
    }
}
