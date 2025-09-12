<?php

namespace Mbsoft\SlrRanking\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Mbsoft\SlrRanking\Models\Project;

class WorkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'project_id' => Project::factory(),
            'doi' => '10.' . $this->faker->numberBetween(1000, 9999) . '/' . $this->faker->lexify('????.????'),
            'title' => $this->faker->sentence(6),
            'abstract' => $this->faker->paragraphs(3, true),
            'year' => $this->faker->numberBetween(2015, 2025),
            'venue_name' => $this->faker->company() . ' Conference',
            'venue_type' => $this->faker->randomElement(['conference', 'journal', 'workshop']),
            'issn' => $this->faker->numerify('####-####'),
            'isbn' => $this->faker->isbn13(),
            'openalex_id' => 'W' . $this->faker->numerify('#########'),
            'arxiv_id' => $this->faker->numerify('####.#####'),
            's2_id' => $this->faker->numerify('#########'),
        ];
    }
}
