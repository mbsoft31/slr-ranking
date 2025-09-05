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

class PullArxivJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $projectId, public array $query) {}

    public function handle(): void
    {
        $base = config('slr-ranking.endpoints.arxiv');
        $start = 0;
        $page = 0;
        $max = 200;
        $sourceId = Source::firstOrCreate(['name' => 'arxiv'])->id;

        while (true) {
            $resp = Http::get($base, [
                'search_query' => $this->query['q'] ?? 'all:agriculture',
                'start' => $start,
                'max_results' => $max,
            ])->throw()->body();

            $xml = simplexml_load_string($resp);
            if (! $xml || ! isset($xml->entry)) {
                break;
            }

            $count = 0;
            foreach ($xml->entry as $entry) {
                $json = json_decode(json_encode($entry), true); // quick array
                RawRecord::create([
                    'id' => (string) Str::uuid(),
                    'project_id' => $this->projectId,
                    'source_id' => $sourceId,
                    'raw_json' => $json,
                    'pulled_at' => now(),
                ]);
                NormalizeAndUpsertWork::dispatch($this->projectId, 'arxiv', $json)->onQueue('normalize');
                $count++;
            }

            if ($count < $max) {
                break;
            } // last page
            $start += $max;
            $page++;
            if ($page > 50) {
                break;
            } // safety
        }
    }
}
