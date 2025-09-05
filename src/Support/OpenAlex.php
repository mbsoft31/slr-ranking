<?php

namespace Mbsoft\SlrRanking\Support;

class OpenAlex
{
    public static function expandAbstract(?array $inverted): ?string
    {
        if (!$inverted) return null;
        $len = 0; foreach ($inverted as $positions) { foreach ($positions as $p) { $len = max($len, $p+1); } }
        $out = array_fill(0,$len,'');
        foreach ($inverted as $word=>$pos) foreach ($pos as $p) $out[$p] = $word;
        return trim(implode(' ', $out));
    }
}
