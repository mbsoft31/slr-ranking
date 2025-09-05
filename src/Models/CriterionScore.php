<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class CriterionScore extends Model
{
    public $incrementing = false; protected $primaryKey='work_id'; protected $keyType='string';
    protected $table = 'slr_criterion_scores';
    protected $fillable = ['work_id','venue_quality','recency','oa_repro','novelty','realism','breadth'];
}
