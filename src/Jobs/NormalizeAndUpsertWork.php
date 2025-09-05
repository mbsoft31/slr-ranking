<?php

namespace Mbsoft\SlrRanking\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Mbsoft\SlrRanking\Services\{NormalizationService,DedupService};

class NormalizeAndUpsertWork implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $projectId, public string $source, public array $raw){}

    public function handle(): void
    {
        $norm = app(NormalizationService::class)->normalize($this->source, $this->raw);
        $work = app(DedupService::class)->findOrCreate($this->projectId, $norm);
        EnrichWorkJob::dispatch($work->id)->onQueue('enrich');
    }
}
