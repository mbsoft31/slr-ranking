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

class PullCrossrefJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $projectId, public array $query) {}

    public function handle(): void
    {
        $base = config('slr-ranking.endpoints.crossref');
        $cursor = '*';
        $sourceId = Source::firstOrCreate(['name' => 'crossref'])->id;

        do {
            $resp = Http::get("$base/works", [
                'query' => $this->query['q'] ?? null,
                'filter' => $this->query['filter'] ?? null, // e.g., 'type:journal-article,from-pub-date:2023-01-01'
                'rows' => 200,
                'cursor' => $cursor,
            ])->throw()->json();

            $items = data_get($resp, 'message.items', []);
            foreach ($items as $w) {
                RawRecord::create([
                    'id' => (string) Str::uuid(),
                    'project_id' => $this->projectId,
                    'source_id' => $sourceId,
                    'raw_json' => $w,
                    'pulled_at' => now(),
                ]);
                NormalizeAndUpsertWork::dispatch($this->projectId, 'crossref', $w)->onQueue('normalize');
            }

            $cursor = data_get($resp, 'message["next-cursor"]');
        } while ($cursor);
    }
}
