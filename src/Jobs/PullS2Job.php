<?php

namespace Mbsoft\SlrRanking\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Mbsoft\SlrRanking\Models\RawRecord;
use Mbsoft\SlrRanking\Models\Source;

class PullS2Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $projectId, public array $query) {}

    public function handle(): void
    {
        $base = rtrim(config('slr-ranking.endpoints.s2'), '/');
        $sourceId = Source::firstOrCreate(['name' => 'semanticscholar'])->id;

        $offset = 0;
        $limit  = 100;
        $q      = $this->query['q'] ?? null;

        while (true) {
            $resp = Http::slr()->get("$base/paper/search", [
                'query'  => $q,
                'fields' => 'title,abstract,venue,year,externalIds,publicationTypes,publicationDate,authors,citationCount,paperId',
                'offset' => $offset,
                'limit'  => $limit,
            ])->throw()->json();

            $items = $resp['data'] ?? [];
            foreach ($items as $w) {
                RawRecord::create([
                    'id'         => (string) Str::uuid(),
                    'project_id' => $this->projectId,
                    'source_id'  => $sourceId,
                    'raw_json'   => $w,
                    'pulled_at'  => now(),
                ]);
                NormalizeAndUpsertWork::dispatch($this->projectId, 's2', $w)->onQueue('normalize');
            }

            if (count($items) < $limit) break;
            $offset += $limit;
            if ($offset > 5_000) break; // safety
        }
    }

}
