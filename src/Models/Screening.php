<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'slr_screenings';

    protected $fillable = ['id', 'work_id', 'stage', 'decision', 'reason', 'reviewer_type', 'reviewer_id', 'decided_at'];

    protected $casts = ['decided_at' => 'datetime'];
}
