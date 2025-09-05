<?php

namespace Mbsoft\SlrRanking\Connectors;

use Mbsoft\SlrRanking\Contracts\Connector;
use Mbsoft\SlrRanking\Jobs\PullArxivJob;

class ArxivConnector implements Connector
{
    public function run(string $projectId, array $query): void
    {
        PullArxivJob::dispatch($projectId, $query)->onQueue('pull');
    }
}
