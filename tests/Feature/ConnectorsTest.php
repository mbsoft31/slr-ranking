<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Mbsoft\SlrRanking\Jobs\{PullArxivJob, PullCrossrefJob, PullS2Job};
use Mbsoft\SlrRanking\Models\{RawRecord, Source};

it('pulls crossref with cursor paging', function () {
    $pid = (string)Str::uuid();
    Http::fake([
        '*/works*' => Http::sequence()
            ->push(['message' => ['items' => [['title' => ['A']]], 'next-cursor' => 'XYZ']])
            ->push(['message' => ['items' => [['title' => ['B']]], 'next-cursor' => null]]),
    ]);
    dispatch_sync(new PullCrossrefJob($pid, ['q' => 'foo']));
    expect(RawRecord::count())->toBe(2);
    expect(Source::whereName('crossref')->exists())->toBeTrue();
});

it('pulls arxiv pages', function () {
    $pid = (string)Str::uuid();
    Http::fake([
        '*export.arxiv.org/api/query*' => Http::sequence()
            ->push('<feed><entry><id>arxiv:1</id><title>T</title><summary>S</summary><published>2023-01-01T00:00:00Z</published></entry></feed>')
            ->push('<feed></feed>'),
    ]);
    dispatch_sync(new PullArxivJob($pid, ['q' => 'all:ml']));
    expect(RawRecord::count())->toBe(1);
});

it('pulls s2 pages', function () {
    $pid = (string)Str::uuid();
    Http::fake([
        '*/paper/search*' => Http::sequence()
            ->push(['data' => [['title' => 'T1', 'paperId' => 'p1']]])
            ->push(['data' => []]),
    ]);
    dispatch_sync(new PullS2Job($pid, ['q' => 'agriculture']));
    expect(RawRecord::count())->toBe(1);
});
