<?php

namespace Mbsoft\SlrRanking\Services;

use Mbsoft\SlrRanking\Models\{Work,Project,Enrichment,VenueMetric,CriterionScore,CompositeScore};

class ScoreService
{
    public function computeForWork(string $workId): void
    {
        $work = Work::with(['enrichment','project','score','composite'])->findOrFail($workId);
        /** @var Project $proj */
        $proj = $work->project;
        $W = collect($proj->weights ?? config('slr-ranking.default_weights'));

        $venue   = $this->venueQuality($work);
        $recency = $this->recency($work->year, $proj->half_life ?? 3);
        $oa      = $this->oaRepro($work);
        $novelty = $work->score->novelty ?? 0;
        $realism = $work->score->realism ?? 0;
        $breadth = $work->score->breadth ?? 0;

        $total = 100 * (
                ($W['venue']??0)*$venue + ($W['recency']??0)*$recency + ($W['oa']??0)*$oa +
                ($W['novelty']??0)*$novelty + ($W['realism']??0)*$realism + ($W['breadth']??0)*$breadth
            );

        CriterionScore::updateOrCreate(['work_id'=>$workId], [
            'venue_quality'=>$venue,'recency'=>$recency,'oa_repro'=>$oa,
            'novelty'=>$novelty,'realism'=>$realism,'breadth'=>$breadth
        ]);

        CompositeScore::updateOrCreate(['work_id'=>$workId], [
            'total'=>$total,
            'breakdown'=>compact('venue','recency','oa','novelty','realism','breadth'),
            'computed_at'=>now()
        ]);
    }

    private function venueQuality(Work $w): float
    {
        $sjr = VenueMetric::where('issn',$w->issn)->latest('snapshot_date')->first();
        $mapSJR = ['Q1'=>1.0,'Q2'=>0.75,'Q3'=>0.5,'Q4'=>0.25];
        $mapCORE= ['A*'=>1.0,'A'=>0.85,'B'=>0.65,'C'=>0.45];

        if ($w->venue_type==='journal' && $sjr?->sjr_quartile) return $mapSJR[$sjr->sjr_quartile] ?? 0.4;
        if ($w->venue_type==='conference' && $sjr?->core_rank) return $mapCORE[$sjr->core_rank] ?? 0.5;
        if ($w->venue_type==='preprint') return 0.35;

        if (!config('slr-ranking.features.citations_percentile_fallback')) return 0.4;
        return min(0.6, $this->citationPercentile($w));
    }

    private function recency(?int $year, float $H=3.0): float
    {
        if (!$year) return 0.0;
        $dt = (int) date('Y') - $year;
        return exp(-log(2) * $dt / $H);
    }

    private function oaRepro(Work $w): float
    {
        $en = $w->enrichment;
        $score = ($en?->is_oa ? 0.6 : 0.0);
        if (!empty($en?->code_urls)) $score += 0.25;
        if (!empty($en?->data_urls)) $score += 0.15;
        return min(1.0, $score);
    }

    private function citationPercentile(Work $w): float
    {
        $ids = $w->project->works()->pluck('id');
        $all = Enrichment::whereIn('work_id',$ids)->pluck('cited_by_count')->filter();
        if ($all->isEmpty()) return 0.0;
        $rank = $all->where('<', $w->enrichment->cited_by_count ?? 0)->count();
        return $rank / max(1,$all->count());
    }
}
