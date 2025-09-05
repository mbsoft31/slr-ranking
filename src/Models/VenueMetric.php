<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class VenueMetric extends Model
{
    public $incrementing = false; protected $keyType='string';
    protected $table = 'slr_venue_metrics';
    protected $fillable = ['id','issn','venue_name','sjr_quartile','core_rank','snapshot_date'];
    protected $casts = ['snapshot_date'=>'date'];
}
