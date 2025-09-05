<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $incrementing = false; protected $keyType='string';
    protected $table = 'slr_audit_logs';
    protected $fillable = ['id','actor','action','payload'];
    protected $casts = ['payload'=>'array'];
}
