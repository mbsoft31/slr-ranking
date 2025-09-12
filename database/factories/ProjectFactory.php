<?php

namespace Mbsoft\SlrRanking\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'name' => $this->faker->sentence(3),
            'objective' => $this->faker->paragraph(2),
            'weights' => [
                'novelty' => $this->faker->randomFloat(2, 0.1, 0.5),
                'methodology' => $this->faker->randomFloat(2, 0.1, 0.4),
                'reproducibility' => $this->faker->randomFloat(2, 0.1, 0.3),
                'impact' => $this->faker->randomFloat(2, 0.1, 0.3),
            ],
            'search_strings' => [
                $this->faker->words(3, true),
                $this->faker->words(2, true),
                $this->faker->words(4, true),
            ],
            'inclusion_criteria' => [
                $this->faker->sentence(),
                $this->faker->sentence(),
                $this->faker->sentence(),
            ],
            'half_life' => $this->faker->randomFloat(1, 2.0, 5.0),
        ];
    }
}
