<?php

namespace Mbsoft\SlrRanking;

use Mbsoft\SlrRanking\Connectors\{OpenAlexConnector,CrossrefConnector,ArxivConnector,S2Connector};

class SlrRanking
{
    public function openalex(): OpenAlexConnector { return app(OpenAlexConnector::class); }
    public function crossref(): CrossrefConnector { return app(CrossrefConnector::class); }
    public function arxiv(): ArxivConnector { return app(ArxivConnector::class); }
    public function s2(): S2Connector { return app(S2Connector::class); }
}
