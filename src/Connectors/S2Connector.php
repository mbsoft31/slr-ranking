<?php

namespace Mbsoft\SlrRanking\Connectors;

use Mbsoft\SlrRanking\Contracts\Connector;
use Mbsoft\SlrRanking\Jobs\PullS2Job;

class S2Connector implements Connector
{
    public function run(string $projectId, array $query): void
    {
        PullS2Job::dispatch($projectId, $query)->onQueue('pull');
    }
}
