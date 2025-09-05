<?php

return [

    'half_life' => 3.0,

    'default_weights' => [
        'venue' => 0.30,
        'recency' => 0.15,
        'oa' => 0.05,

        'novelty' => 0.20,
        'realism' => 0.20,
        'breadth' => 0.10,
    ],

    'user_model' => \App\Models\User::class,

    'endpoints' => [
        'openalex' => env('OPENALEX_BASE', 'https://api.openalex.org'),
        'crossref' => env('CROSSREF_BASE', 'https://api.crossref.org'),
        'unpaywall' => 'https://api.unpaywall.org/v2',
        's2' => env('S2_BASE', 'https://api.semanticscholar.org/graph/v1/'),
        'arxiv' => env('ARXIV_BASE', 'https://export.arxiv.org/api'),
    ],

    'http' => [
        // Leave true in prod; if you must work around local CA issues, set HTTP_VERIFY=false in .env temporarily.
        'verify'          => env('HTTP_VERIFY', true),
        'timeout'         => env('HTTP_TIMEOUT', 60),
        'connect_timeout' => env('HTTP_CONNECT_TIMEOUT', 10),
        'user_agent'      => env('HTTP_UA', 'SLR-Ranking/0.1 (+you@example.com)'),
    ],

    // Optional: S2 Graph v1 sometimes requires an API key (or throttles heavily).
    's2' => [
        'api_key' => env('S2_API_KEY', null),
    ],

    'unpaywall_email' => env('UNPAYWALL_EMAIL'),

    // Optional: 'scout' or null
    'search_driver' => env('SLR_SEARCH', null),

    'features' => [
        'citations_percentile_fallback' => true,
    ],
];


