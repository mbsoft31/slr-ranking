<?php

namespace Mbsoft\SlrRanking\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Mbsoft\SlrRanking\Models\Project;

class ProjectFactory extends Factory
{
    protected $model = Project::class;
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'name' => fake()->sentence(3),
            'objective' => fake()->paragraph(2),
            'weights' => [
                'novelty' => fake()->randomFloat(2, 0.1, 0.5),
                'methodology' => fake()->randomFloat(2, 0.1, 0.4),
                'reproducibility' => fake()->randomFloat(2, 0.1, 0.3),
                'impact' => fake()->randomFloat(2, 0.1, 0.3),
            ],
            'search_strings' => [
                fake()->words(3, true),
                fake()->words(2, true),
                fake()->words(4, true),
            ],
            'inclusion_criteria' => [
                fake()->sentence(),
                fake()->sentence(),
                fake()->sentence(),
            ],
            'half_life' => fake()->randomFloat(1, 2.0, 5.0),
        ];
    }
}
