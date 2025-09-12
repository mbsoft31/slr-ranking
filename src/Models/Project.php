<?php

namespace Mbsoft\SlrRanking\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mbsoft\SlrRanking\Database\Factories\ProjectFactory;

class Project extends Model
{
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'slr_projects';

    protected $fillable = ['id', 'name', 'objective', 'weights', 'search_strings', 'inclusion_criteria', 'half_life'];

    protected $casts = ['weights' => 'array', 'search_strings' => 'array', 'inclusion_criteria' => 'array', 'half_life' => 'float'];

    public function works(): HasMany
    {
        return $this->hasMany(Work::class, 'project_id');
    }

    protected static function newFactory(): ProjectFactory
    {
        return ProjectFactory::new();
    }
}
