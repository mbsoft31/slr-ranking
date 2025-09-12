<?php

namespace Mbsoft\SlrRanking\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Mbsoft\SlrRanking\Models\Project;
use Mbsoft\SlrRanking\Models\Work;

class WorkFactory extends Factory
{
    protected $model = Work::class;
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'project_id' => Project::factory(),
            'doi' => '10.' . fake()->numberBetween(1000, 9999) . '/' . fake()->lexify('????.????'),
            'title' => fake()->sentence(6),
            'abstract' => fake()->paragraphs(3, true),
            'year' => fake()->numberBetween(2015, 2025),
            'venue_name' => fake()->company() . ' Conference',
            'venue_type' => fake()->randomElement(['conference', 'journal', 'workshop']),
            'issn' => fake()->numerify('####-####'),
            'isbn' => fake()->isbn13(),
            'openalex_id' => 'W' . fake()->numerify('#########'),
            'arxiv_id' => fake()->numerify('####.#####'),
            's2_id' => fake()->numerify('#########'),
        ];
    }
}
