<?php

namespace Mbsoft\SlrRanking\Contracts;

interface Connector
{
    public function run(string $projectId, array $query): void;
}
