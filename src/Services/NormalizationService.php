<?php

namespace Mbsoft\SlrRanking\Services;

use Mbsoft\SlrRanking\Support\OpenAlex;

class NormalizationService
{
    /**
     * @return array: #ArrayShape['doi' => string|null, 'title' => string|null, 'abstract' => string|null, 'year' => int|null, 'venue_name' => string|null, 'venue_type' => string|null,'issn' => string|null,'isbn' => string|null,'openalex_id' => string|null,'arxiv_id' => string|null,'s2_id' => string|null]
     */
    public function normalize(string $source, array $raw): array
    {
        if ($source === 'openalex') {
            return [
                'doi' => data_get($raw, 'doi'),
                'title' => trim((string) data_get($raw, 'title')),
                'abstract' => OpenAlex::expandAbstract(data_get($raw, 'abstract_inverted_index') ?? null),
                'year' => (int) data_get($raw, 'publication_year'),
                'venue_name' => data_get($raw, 'host_venue.display_name'),
                'venue_type' => data_get($raw, 'host_venue.type'),
                'issn' => data_get($raw, 'host_venue.issn.0'),
                'openalex_id' => data_get($raw, 'id'),
                's2_id' => data_get($raw, 'ids.semantic_scholar'),
            ];
        }

        // Crossref
        if ($source === 'crossref') {
            $doi = data_get($raw, 'DOI');
            $title = trim((string) (data_get($raw, 'title.0') ?? ''));
            $year = (int) (data_get($raw, 'issued.date-parts.0.0')
                ?? data_get($raw, 'published-print.date-parts.0.0')
                ?? data_get($raw, 'published-online.date-parts.0.0'));
            $venue = data_get($raw, 'container-title.0');
            $type = data_get($raw, 'type');
            $venueType = str_contains((string) $type, 'journal') ? 'journal'
                : (str_contains((string) $type, 'proceedings') ? 'conference' : null);
            $issn = data_get($raw, 'ISSN.0');

            $abs = data_get($raw, 'abstract');
            if ($abs) {
                $abs = preg_replace('/<\/?jats:[^>]+>/', '', $abs);
                $abs = strip_tags($abs);
                $abs = html_entity_decode($abs, ENT_QUOTES | ENT_XML1);
                $abs = trim(preg_replace('/\s+/', ' ', $abs));
            }

            return [
                'doi' => $doi ?: null,
                'title' => $title ?: null,
                'abstract' => $abs ?: null,
                'year' => $year ?: null,
                'venue_name' => $venue,
                'venue_type' => $venueType,
                'issn' => $issn,
                'openalex_id' => null,
                'arxiv_id' => null,
                's2_id' => null,
            ];
        }

        // arXiv
        if ($source === 'arxiv') {
            $id = data_get($raw, 'id');
            $title = trim(preg_replace('/\s+/', ' ', (string) data_get($raw, 'title', '')));
            $summary = trim((string) data_get($raw, 'summary', ''));
            $pub = (string) data_get($raw, 'published');
            $year = $pub ? (int) substr($pub, 0, 4) : null;
            $doi = data_get($raw, 'doi') ?: null;

            return [
                'doi' => $doi,
                'title' => $title ?: null,
                'abstract' => $summary ?: null,
                'year' => $year,
                'venue_name' => 'arXiv',
                'venue_type' => 'preprint',
                'issn' => null,
                'openalex_id' => null,
                'arxiv_id' => $id,
                's2_id' => null,
            ];
        }

        // S2
        if ($source === 's2') {
            $doi = data_get($raw, 'externalIds.DOI');
            $title = trim((string) data_get($raw, 'title', ''));
            $abs = trim((string) data_get($raw, 'abstract', ''));
            $year = (int) data_get($raw, 'year');
            $venue = data_get($raw, 'venue');
            $types = (array) data_get($raw, 'publicationTypes', []);
            $venueType = in_array('JournalArticle', $types, true) ? 'journal'
                : (in_array('Conference', $types, true) ? 'conference' : null);

            return [
                'doi' => $doi ?: null,
                'title' => $title ?: null,
                'abstract' => $abs ?: null,
                'year' => $year ?: null,
                'venue_name' => $venue,
                'venue_type' => $venueType,
                'issn' => null,
                'openalex_id' => null,
                'arxiv_id' => null,
                's2_id' => (string) data_get($raw, 'paperId'),
            ];
        }

        return [];
    }
}
