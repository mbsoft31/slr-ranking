<?php

namespace Mbsoft\SlrRanking\Services;

use Illuminate\Support\Str;
use Mbsoft\SlrRanking\Models\Work;
use Mbsoft\SlrRanking\Support\Fuzzy;

class DedupService
{
    public function findOrCreate(string $projectId, array $norm): Work
    {
        if (! empty($norm['doi'])) {
            $byDoi = Work::where('project_id', $projectId)->where('doi', $norm['doi'])->first();
            if ($byDoi) {
                $byDoi->update(array_filter($norm));

                return $byDoi;
            }
        }

        $year = $norm['year'] ?? null;
        $title = mb_strtolower($norm['title'] ?? '');
        $candidates = Work::where('project_id', $projectId)
            ->when($year, fn ($q) => $q->whereBetween('year', [$year - 1, $year + 1]))->get();

        foreach ($candidates as $c) {
            $score = Fuzzy::tokenSetRatio($title, mb_strtolower((string) $c->title));
            if ($score >= 92) {
                $c->update(array_filter($norm));

                return $c;
            }
        }

        $norm['id'] = (string) Str::uuid();

        return Work::create($norm + ['project_id' => $projectId]);
    }
}
