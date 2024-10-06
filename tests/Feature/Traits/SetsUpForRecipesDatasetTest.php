<?php

namespace Tests\Feature\Traits;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;

trait SetsUpForRecipesDatasetTest
{
    protected function setupForRecipesDatasetTests(): array
    {
        $expectedRecipes = Recipe::factory()->count(4)
            ->has(
                Ingredient::factory()->count(3)
            )
            ->has(
                Step::factory()->count(4)
                    ->sequence(
                        [
                            'step_number' => 2,
                        ],
                        [
                            'step_number' => 1,
                        ],
                        [
                            'step_number' => 3,
                        ],
                        [
                            'step_number' => 4,
                        ],
                    )
            )
            ->create()
            ->sortBy('name')
            ->values();

        return [
            'expectedRecipes' => $expectedRecipes,
        ];
    }
}
