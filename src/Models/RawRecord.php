<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class RawRecord extends Model
{
    public $incrementing = false; protected $keyType='string';
    protected $table = 'slr_raw_records';
    protected $fillable = ['id','project_id','source_id','raw_json','pulled_at'];
    protected $casts = ['raw_json'=>'array','pulled_at'=>'datetime'];
}
