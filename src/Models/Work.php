<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'slr_works';

    protected $fillable = ['id', 'project_id', 'doi', 'title', 'abstract', 'year', 'venue_name', 'venue_type', 'issn', 'isbn', 'openalex_id', 'arxiv_id', 's2_id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function enrichment()
    {
        return $this->hasOne(Enrichment::class, 'work_id');
    }

    public function score()
    {
        return $this->hasOne(CriterionScore::class, 'work_id');
    }

    public function composite()
    {
        return $this->hasOne(CompositeScore::class, 'work_id');
    }
}
