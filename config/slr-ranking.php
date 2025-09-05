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
        's2' => env('S2_BASE', 'https://api.semanticscholar.org/graph/v1'),
        'arxiv' => env('ARXIV_BASE', 'https://export.arxiv.org/api'),
    ],

    'unpaywall_email' => env('UNPAYWALL_EMAIL'),

    // Optional: 'scout' or null
    'search_driver' => env('SLR_SEARCH', null),

    'features' => [
        'citations_percentile_fallback' => true,
    ],
];
