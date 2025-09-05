<?php

namespace Mbsoft\SlrRanking\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mbsoft\SlrRanking\SlrRanking
 */
class SlrRanking extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mbsoft\SlrRanking\SlrRanking::class;
    }
}
