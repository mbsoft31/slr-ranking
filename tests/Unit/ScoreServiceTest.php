<?php

use Illuminate\Support\Str;
use Mbsoft\SlrRanking\Models\{Enrichment, LookupsCore, LookupsSjr, Project, Work};
use Mbsoft\SlrRanking\Services\ScoreService;

it('maps journal quartile via ISSN', function () {
    LookupsSjr::create(['issn' => '1234-5678', 'quartile' => 'Q1', 'snapshot_date' => now()]);
    $p = Project::create([
        'id'=>(string)Str::uuid(),'name'=>'P','objective'=>'custom',
        'weights'=>config('slr-ranking.default_weights'),
        'search_strings'=>[],'inclusion_criteria'=>[],'half_life'=>3,
    ]);

    $w = Work::create([
        'id'=>(string)Str::uuid(),
        'project_id'=>$p->id,
        'issn'=>'1234-5678',
        'venue_type'=>'journal',
        'venue_name'=>'Test',
        'title'=>'Placeholder title',
        'year'=>2024,
    ]);
    $svc = app(ScoreService::class);
    $ref = (new ReflectionClass($svc))->getMethod('venueScore');
    $ref->setAccessible(true);
    expect($ref->invoke($svc, $w))->toBe(1.0);
});

it('maps conference via normalized name to CORE', function () {
    LookupsCore::create(['conference' => 'International Foo Bar Conference', 'rank' => 'A', 'snapshot_date' => now()]);
    $p = Project::create([
        'id'=>(string)Str::uuid(),'name'=>'P','objective'=>'custom',
        'weights'=>config('slr-ranking.default_weights'),
        'search_strings'=>[],'inclusion_criteria'=>[],'half_life'=>3,
    ]);

    $w = Work::create([
        'id'=>(string)Str::uuid(),
        'project_id'=>$p->id,
        'venue_type'=>'conference',
        'venue_name'=>'International Foo Bar Conference',
        'title'=>'Placeholder title',
        'year'=>2024,
    ]);
    $svc = app(ScoreService::class);
    $ref = (new ReflectionClass($svc))->getMethod('venueScore');
    $ref->setAccessible(true);
    $value = $ref->invoke($svc, $w);
    expect($value)->toBeGreaterThan(0.84)->and($value)->toBeLessThan(0.86);
});

it('computes citation percentile correctly', function () {
    // Project
    $p = Project::create([
        'id' => (string) Str::uuid(),
        'name' => 'P',
        'objective' => 'custom',
        'weights' => config('slr-ranking.default_weights'),
        'search_strings' => [],
        'inclusion_criteria' => [],
        'half_life' => 3,
    ]);

    // Base work (the “subject” with 50 cites)
    $w = Work::create([
        'id'         => (string) Str::uuid(),
        'project_id' => $p->id,
        'title'      => 'Base work',
        'year'       => 2024,
        'venue_name' => 'Test',
        'venue_type' => 'journal',
    ]);
    Enrichment::create(['work_id' => $w->id, 'cited_by_count' => 50]);

    // Comparison set (ensure every work has a title)
    foreach ([0, 10, 25, 50, 100] as $c) {
        $wid = (string) Str::uuid();
        Work::create([
            'id'         => $wid,
            'project_id' => $p->id,
            'title'      => "W-$c",
            'year'       => 2024,
            'venue_name' => 'Test',
            'venue_type' => 'journal',
        ]);
        Enrichment::create(['work_id' => $wid, 'cited_by_count' => $c]);
    }

    // Exclude self in percentile → {0,10,25} below 50 out of 5 = 60%
    $svc = app(ScoreService::class);
    $ref = (new ReflectionClass($svc))->getMethod('citationPercentile');
    $ref->setAccessible(true);

    $pct = $ref->invoke($svc, $w);
    expect($pct)->toBe(60.00);
});
