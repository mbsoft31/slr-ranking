<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class LookupsSjr extends Model
{
    protected $table = 'slr_lookups_sjr';

    protected $fillable = ['issn', 'quartile', 'snapshot_date'];

    protected $casts = ['snapshot_date' => 'date'];
}
