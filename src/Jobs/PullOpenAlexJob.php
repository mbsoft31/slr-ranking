<?php

namespace Mbsoft\SlrRanking\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Mbsoft\SlrRanking\Models\RawRecord;
use Mbsoft\SlrRanking\Models\Source;

class PullOpenAlexJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $projectId, public array $query) {}

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $base = config('slr-ranking.endpoints.openalex');
        $cursor = '*';
        $sourceId = Source::firstOrCreate(['name' => 'openalex'])->id;

        do {
            $resp = Http::get("$base/works", [
                'search' => $this->query['q'] ?? null,
                'filter' => $this->query['filter'] ?? null,
                'per-page' => 200,
                'cursor' => $cursor,
            ])->throw()->json();

            foreach (($resp['results'] ?? []) as $w) {
                RawRecord::create([
                    'id' => (string) Str::uuid(),
                    'project_id' => $this->projectId,
                    'source_id' => $sourceId,
                    'raw_json' => $w,
                    'pulled_at' => now(),
                ]);
                NormalizeAndUpsertWork::dispatch($this->projectId, 'openalex', $w)->onQueue('normalize');
            }

            $cursor = $resp['meta']['next_cursor'] ?? null;
        } while ($cursor);
    }
}
