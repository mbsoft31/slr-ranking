<?php

namespace Mbsoft\SlrRanking\Services;

use Illuminate\Support\Str;
use Mbsoft\SlrRanking\Models\LookupsCore;
use Mbsoft\SlrRanking\Models\LookupsSjr;
use Mbsoft\SlrRanking\Models\Work;

class VenueResolverService
{
    public function venueQualityFor(Work $w): ?float
    {
        if ($w->venue_type === 'journal' && $w->issn) {
            $sjr = LookupsSjr::where('issn', $w->issn)->orderByDesc('snapshot_date')->first();
            if ($sjr) {
                return [
                    'Q1' => 1.00, 'Q2' => 0.75, 'Q3' => 0.50, 'Q4' => 0.25,
                ][$sjr->quartile] ?? 0.40;
            }
        }

        if ($w->venue_type === 'conference' && $w->venue_name) {
            $needle = $this->normalizeName($w->venue_name);
            $core = LookupsCore::get()->first(function ($row) use ($needle) {
                return $this->normalizeName($row->conference) === $needle;
            });
            if ($core) {
                return [
                    'A*' => 1.00, 'A' => 0.85, 'B' => 0.65, 'C' => 0.45,
                ][$core->rank] ?? 0.50;
            }
        }

        if ($w->venue_type === 'preprint') {
            return 0.35;
        }

        return null;
    }

    protected function normalizeName(string $s): string
    {
        $s = Str::lower($s);
        $s = preg_replace('/[^a-z0-9]+/', '', $s);

        return $s;
    }
}
