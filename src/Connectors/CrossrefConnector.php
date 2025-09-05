<?php

namespace Mbsoft\SlrRanking\Connectors;

use Mbsoft\SlrRanking\Contracts\Connector;
use Mbsoft\SlrRanking\Jobs\PullCrossrefJob;

class CrossrefConnector implements Connector
{
    public function run(string $projectId, array $query): void
    {
        PullCrossrefJob::dispatch($projectId, $query)->onQueue('pull');
    }
}
