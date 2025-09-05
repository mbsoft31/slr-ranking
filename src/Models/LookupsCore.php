<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class LookupsCore extends Model
{
    protected $table = 'slr_lookups_core';

    protected $fillable = ['conference', 'rank', 'snapshot_date'];

    protected $casts = ['snapshot_date' => 'date'];
}
