<?php

namespace Mbsoft\SlrRanking\Services;

use Illuminate\Support\Facades\Http;
use Mbsoft\SlrRanking\Models\Enrichment;
use Mbsoft\SlrRanking\Models\Work;

class EnrichmentService
{
    public function unpaywall(string $workId): void
    {
        $work = Work::findOrFail($workId);
        if (! $work->doi) {
            return;
        }
        $email = config('slr-ranking.unpaywall_email');
        $resp = Http::slr()->get(config('slr-ranking.endpoints.unpaywall')."/{$work->doi}", ['email'=>$email]);
        if ($resp->ok()) {
            $j = $resp->json();
            Enrichment::updateOrCreate(['work_id' => $workId], [
                'is_oa' => (bool) data_get($j, 'is_oa'),
                'oa_url' => data_get($j, 'best_oa_location.url_for_pdf') ?? data_get($j, 'best_oa_location.url'),
            ]);
        }
    }

    public function codeAndDataLinks(string $workId): void
    {
        $work = Work::with('enrichment')->findOrFail($workId);
        $hay = implode(' ', array_filter([$work->abstract, $work->title]));
        preg_match_all('#https?://[^\s)]+#', $hay, $m);
        $urls = collect($m[0] ?? []);
        $code = $urls->filter(fn ($u) => str_contains($u, 'github.com') || str_contains($u, 'gitlab.com') || str_contains($u, 'zenodo.org/records/'));
        $data = $urls->filter(fn ($u) => str_contains($u, 'zenodo.org') || str_contains($u, 'figshare.com') || str_contains($u, 'dataverse'));
        $work->enrichment()->updateOrCreate(['work_id' => $workId], [
            'code_urls' => $code->values()->all(),
            'data_urls' => $data->values()->all(),
        ]);
    }

    public function citations(string $workId): void
    {
        $work = Work::with('enrichment')->findOrFail($workId);

        if ($work->openalex_id) {
            $id = basename($work->openalex_id);
            $o = Http::get(config('slr-ranking.endpoints.openalex')."/works/{$id}")->json();
            $work->enrichment()->updateOrCreate(['work_id' => $workId], [
                'cited_by_count' => (int) data_get($o, 'cited_by_count', 0),
                'fields' => data_get($o, 'concepts'),
            ]);

            return;
        }

        // Fallback: S2 by DOI (if present)
        if ($work->doi) {
            $s2 = Http::get(config('slr-ranking.endpoints.s2')."/paper/DOI:{$work->doi}", [
                'fields' => 'citationCount,fieldsOfStudy',
            ]);
            if ($s2->ok()) {
                $j = $s2->json();
                $work->enrichment()->updateOrCreate(['work_id' => $workId], [
                    'cited_by_count' => (int) data_get($j, 'citationCount', 0),
                    'fields' => data_get($j, 'fieldsOfStudy'),
                ]);
            }
        }
    }
}
