<?php

namespace Mbsoft\SlrRanking\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Mbsoft\SlrRanking\Models\{RawRecord,Source};

class PullS2Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $projectId, public array $query){}

    public function handle(): void
    {
        $base = config('slr-ranking.endpoints.s2');
        $offset = 0; $limit = 100;
        $sourceId = Source::firstOrCreate(['name'=>'semanticscholar'])->id;

        while (true) {
            $resp = Http::get("$base/paper/search", [
                'query'  => $this->query['q'] ?? null,
                'fields' => 'title,abstract,venue,year,externalIds,publicationTypes,publicationDate,authors,citationCount',
                'offset' => $offset,
                'limit'  => $limit,
            ])->throw()->json();

            $items = $resp['data'] ?? [];
            foreach ($items as $w) {
                RawRecord::create([
                    'id'=> (string) Str::uuid(),
                    'project_id'=> $this->projectId,
                    'source_id'=> $sourceId,
                    'raw_json'=> $w,
                    'pulled_at'=> now(),
                ]);
                NormalizeAndUpsertWork::dispatch($this->projectId, 's2', $w)->onQueue('normalize');
            }

            if (count($items) < $limit) break;
            $offset += $limit;
            if ($offset > 5000) break; // safety
        }
    }
}
