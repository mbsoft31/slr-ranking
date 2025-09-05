<?php

namespace Mbsoft\SlrRanking\Services;

use Mbsoft\SlrRanking\Support\OpenAlex;

class NormalizationService
{
    public function normalize(string $source, array $raw): array
    {
        if ($source === 'openalex') {
            return [
                'doi' => data_get($raw, 'doi'),
                'title' => trim((string) data_get($raw, 'title')),
                'abstract' => OpenAlex::expandAbstract(data_get($raw, 'abstract_inverted_index') ?? null),
                'year' => (int) data_get($raw, 'publication_year'),
                'venue_name' => data_get($raw, 'host_venue.display_name'),
                'venue_type' => data_get($raw, 'host_venue.type'), // journal|conference|repository
                'issn' => data_get($raw, 'host_venue.issn.0'),
                'openalex_id' => data_get($raw, 'id'),
                's2_id' => data_get($raw, 'ids.semantic_scholar'),
            ];
        }

        if ($source === 'crossref') {
            // $raw is a Crossref "work" item
            $doi = data_get($raw, 'DOI');
            $titleArr = (array) data_get($raw, 'title', []);
            $title = trim((string) ($titleArr[0] ?? ''));
            $year = (int) (data_get($raw, 'issued.date-parts.0.0') ?? data_get($raw, 'published-print.date-parts.0.0'));
            $venue = data_get($raw, 'container-title.0');
            $type = data_get($raw, 'type'); // journal-article, proceedings-article, etc.
            $venueType = str_contains($type, 'journal') ? 'journal' : (str_contains($type, 'proceedings') ? 'conference' : null);
            $issn = data_get($raw, 'ISSN.0');

            return [
                'doi' => $doi,
                'title' => $title,
                'abstract' => null, // Crossref abstracts are often not present (or JATS-XML)
                'year' => $year ?: null,
                'venue_name' => $venue,
                'venue_type' => $venueType,
                'issn' => $issn,
                'openalex_id' => null,
                's2_id' => null,
            ];
        }

        if ($source === 'arxiv') {
            // $raw is an array from SimpleXML→json
            $id = data_get($raw, 'id');
            $title = trim(preg_replace('/\s+/', ' ', (string) data_get($raw, 'title', '')));
            $summary = trim((string) data_get($raw, 'summary', ''));
            $published = (string) data_get($raw, 'published');
            $year = $published ? (int) substr($published, 0, 4) : null;
            $doi = data_get($raw, 'doi'); // sometimes present

            return [
                'doi' => $doi,
                'title' => $title,
                'abstract' => $summary,
                'year' => $year,
                'venue_name' => 'arXiv',
                'venue_type' => 'preprint',
                'issn' => null,
                'openalex_id' => null,
                'arxiv_id' => $id,
                's2_id' => null,
            ];
        }

        if ($source === 's2') {
            $doi = data_get($raw, 'externalIds.DOI');
            $title = trim((string) data_get($raw, 'title', ''));
            $abstract = (string) data_get($raw, 'abstract', '');
            $year = (int) data_get($raw, 'year');
            $venue = data_get($raw, 'venue');
            $pubTypes = (array) data_get($raw, 'publicationTypes', []);
            $venueType = null;
            if (in_array('JournalArticle', $pubTypes)) {
                $venueType = 'journal';
            }
            if (in_array('Conference', $pubTypes)) {
                $venueType = 'conference';
            }

            return [
                'doi' => $doi,
                'title' => $title,
                'abstract' => $abstract ?: null,
                'year' => $year ?: null,
                'venue_name' => $venue,
                'venue_type' => $venueType,
                'issn' => null,
                'openalex_id' => null,
                's2_id' => (string) data_get($raw,'paperId'),
            ];
        }

        return [];
    }
}
