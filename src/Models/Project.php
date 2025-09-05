<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'slr_projects';

    protected $fillable = ['id', 'name', 'objective', 'weights', 'search_strings', 'inclusion_criteria', 'half_life'];

    protected $casts = ['weights' => 'array', 'search_strings' => 'array', 'inclusion_criteria' => 'array', 'half_life' => 'float'];

    public function works()
    {
        return $this->hasMany(Work::class, 'project_id');
    }
}
