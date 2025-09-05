<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $table = 'slr_sources';
    protected $fillable = ['name'];
}
