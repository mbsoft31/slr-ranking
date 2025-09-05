<?php

namespace Mbsoft\SlrRanking\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mbsoft\SlrRanking\Services\ScoreService;

class ComputeScoresJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $workId) {}

    public function handle(): void
    {
        app(ScoreService::class)->computeForWork($this->workId);
    }
}
