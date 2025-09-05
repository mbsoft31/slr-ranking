<?php

namespace Mbsoft\SlrRanking\Support;

class Fuzzy
{
    public static function tokenSetRatio(string $a, string $b): int
    {
        $ta = collect(preg_split('/\W+/u', $a, -1, PREG_SPLIT_NO_EMPTY))->unique()->sort()->implode(' ');
        $tb = collect(preg_split('/\W+/u', $b, -1, PREG_SPLIT_NO_EMPTY))->unique()->sort()->implode(' ');
        similar_text($ta, $tb, $pct);
        return (int) round($pct);
    }
}
