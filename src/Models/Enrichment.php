<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class Enrichment extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'work_id';

    protected $keyType = 'string';

    protected $table = 'slr_enrichments';

    protected $fillable = ['work_id', 'is_oa', 'oa_url', 'code_urls', 'data_urls', 'cited_by_count', 'fields'];

    protected $casts = ['is_oa' => 'boolean', 'code_urls' => 'array', 'data_urls' => 'array', 'fields' => 'array'];
}
