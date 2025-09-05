<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertReview extends Model
{
    public $incrementing = false; protected $keyType='string';
    protected $table = 'slr_expert_reviews';
    protected $fillable = ['id','work_id','note','adjustment_delta','final_rank_override','reviewer_type','reviewer_id','decided_at'];
    protected $casts = ['decided_at'=>'datetime'];
}
