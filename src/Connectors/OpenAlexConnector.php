<?php

namespace Mbsoft\SlrRanking\Connectors;

use Mbsoft\SlrRanking\Contracts\Connector;
use Mbsoft\SlrRanking\Jobs\PullOpenAlexJob;

class OpenAlexConnector implements Connector
{
    public function run(string $projectId, array $query): void
    {
        PullOpenAlexJob::dispatch($projectId, $query)->onQueue('pull');
    }
}
