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
        $base = rtrim(config('slr-ranking.endpoints.arxiv'), '/'); // usually https://export.arxiv.org/api
        $sourceId = Source::firstOrCreate(['name' => 'arxiv'])->id;

        $start = 0;
        $pageSize = min(200, (int) ($this->query['max_results'] ?? 200));
        $q = $this->query['q'] ?? 'all:agriculture';

        while (true) {
            $resp = Http::slr()->get($base.'/query', [
                'search_query' => $q,
                'start' => $start,
                'max_results' => $pageSize,
            ])->throw()->body();

            $xml = @simplexml_load_string($resp);
            if (! $xml || ! isset($xml->entry)) {
                break;
            }

            $count = 0;
            foreach ($xml->entry as $entry) {
                $json = json_decode(json_encode($entry), true);
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

            if ($count < $pageSize) {
                break;
            }
            $start += $pageSize;
            if ($start > 10_000) {
                break;
            } // safety
        }
    }
}
