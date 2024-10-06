<?php

namespace Tests\Feature\Controllers\RecipeControllerTest;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;

trait SharedRecipeControllerTestMethods
{
    protected function getExpectedRecipe(Recipe $recipe): array
    {
        $recipe->loadMissing([
            'ingredients',
            'steps',
        ]);

        return [
            'slug' => $recipe->slug,
            'name' => $recipe->name,
            'description' => $recipe->description,
            'author_email' => $recipe->author_email,
            'ingredients' => $recipe->ingredients
                ->map(function (Ingredient $ingredient) {
                    return $ingredient->only('description');
                })
                ->toArray(),
            'steps' => $recipe->steps
                ->sortBy('step_number')
                ->values()
                ->map(function (Step $step) {
                    return $step->only([
                        'step_number',
                        'description',
                    ]);
                })
                ->toArray(),
        ];
    }
}
