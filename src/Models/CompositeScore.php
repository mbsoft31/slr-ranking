<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class CompositeScore extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'work_id';

    protected $keyType = 'string';

    protected $table = 'slr_composite_scores';

    protected $fillable = ['work_id', 'total', 'breakdown', 'computed_at'];

    protected $casts = ['breakdown' => 'array', 'computed_at' => 'datetime'];
}
