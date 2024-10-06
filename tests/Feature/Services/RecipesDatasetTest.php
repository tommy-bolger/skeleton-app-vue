<?php

namespace Tests\Feature\Services;

use App\Enums\SortColumns;
use App\Enums\SortDirections;
use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;
use App\Services\RecipesDataset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\Feature\Traits\SetsUpForRecipesDatasetTest;
use Tests\TestCase;

class RecipesDatasetTest extends TestCase
{
    use RefreshDatabase;
    use SetsUpForRecipesDatasetTest;

    private function runIngredientsAssertions(Collection $expectedIngredients, Collection $actualIngredients): void
    {
        foreach ($actualIngredients as $index => $actualIngredient) {
            $this->assertInstanceOf(Ingredient::class, $actualIngredient);
            $this->assertSame($expectedIngredients[$index]->id, $actualIngredient->id);
        }
    }

    private function runStepsAssertions(Collection $expectedSteps, Collection $actualSteps): void
    {
        $expectedSteps = $expectedSteps->sortBy('step_number')
            ->values();

        foreach ($actualSteps as $index => $actualStep) {
            $this->assertInstanceOf(Step::class, $actualStep);
            $this->assertSame($expectedSteps[$index]->id, $actualStep->id);
        }
    }

    public function test_paginate_returns_correct_dataset_for_default_state(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $dataset = new RecipesDataset();
        $paginator = $dataset->paginate();

        $this->assertCount($expectedRecipes->count(), $paginator->items());

        /** @var Recipe $actualRecipe */
        foreach ($paginator->items() as $index => $actualRecipe) {
            /** @var Recipe $expectedRecipe */
            $expectedRecipe = $expectedRecipes[$index];
            $expectedRecipe->loadMissing([
                'ingredients',
                'steps',
            ]);

            $this->assertInstanceOf(Recipe::class, $actualRecipe);
            $this->assertSame($expectedRecipe->id, $actualRecipe->id);

            $this->runIngredientsAssertions($expectedRecipe->ingredients, $actualRecipe->ingredients);
            $this->runStepsAssertions($expectedRecipe->steps, $actualRecipe->steps);
        }
    }

    public function test_paginate_sorts_dataset_in_ascending_order_for_each_sortable_column(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $dataset = new RecipesDataset();

        foreach (SortColumns::cases() as $sortColumn) {
            $paginator = $dataset->setSort($sortColumn, SortDirections::ASC)
                ->paginate();

            $expectedRecipes = $expectedRecipes->sortBy($sortColumn->value)
                ->values();

            /** @var Recipe $actualRecipe */
            foreach ($paginator->items() as $index => $actualRecipe) {
                $this->assertSame($expectedRecipes[$index]->id, $actualRecipe->id);
            }
        }
    }

    public function test_paginate_sorts_dataset_in_descending_order_for_each_sortable_column(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $dataset = new RecipesDataset();

        foreach (SortColumns::cases() as $sortColumn) {
            $paginator = $dataset->setSort($sortColumn, SortDirections::DESC)
                ->paginate();

            $expectedRecipes = $expectedRecipes->sortByDesc($sortColumn->value)
                ->values();

            /** @var Recipe $actualRecipe */
            foreach ($paginator->items() as $index => $actualRecipe) {
                $this->assertSame($expectedRecipes[$index]->id, $actualRecipe->id);
            }
        }
    }

    public function test_set_pagination_paginates_dataset_correctly(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $dataset = new RecipesDataset();
        $paginator = $dataset->setPagination(2, 2)
            ->paginate();

        $actualRecipes = $paginator->items();

        $this->assertCount(2, $actualRecipes);

        /*
         * The first and second records in the dataset should be the third
         * and fourth expected recipes respectively.
         */
        $this->assertSame($expectedRecipes[2]->id, $actualRecipes[0]->id);
        $this->assertSame($expectedRecipes[3]->id, $actualRecipes[1]->id);
    }

    public function test_set_pagination_defaults_page_to_1_when_specified_page_is_less_than_1(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $dataset = new RecipesDataset();
        $paginator = $dataset->setPagination(0, 2)
            ->paginate();

        $actualRecipes = $paginator->items();

        $this->assertCount(2, $actualRecipes);

        /*
         * The first and second records in the dataset should be the first
         * and second expected recipes respectively.
         */
        $this->assertSame($expectedRecipes[0]->id, $actualRecipes[0]->id);
        $this->assertSame($expectedRecipes[1]->id, $actualRecipes[1]->id);
    }

    public function test_set_pagination_defaults_limit_to_25_when_specified_limit_is_out_of_bounds(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $dataset = new RecipesDataset();

        foreach ([0, RecipesDataset::MAX_LIMIT + 1] as $badLimit) {
            $paginator = $dataset->setPagination(1, $badLimit)
                ->paginate();

            // All expected recipes should be returned.
            $this->assertCount($expectedRecipes->count(), $paginator->items());

            // The limit set in the paginator should be the max allowed.
            $this->assertSame(RecipesDataset::MAX_LIMIT, $paginator->perPage());
        }
    }

    public function test_set_author_email_correctly_filters_dataset(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        /** @var Recipe $expectedRecipe */
        $expectedRecipe = $expectedRecipes[1];
        $expectedRecipe->author_email = 'foo@bar.test';
        $expectedRecipe->save();

        $dataset = new RecipesDataset();
        $paginator = $dataset->setAuthorEmail($expectedRecipe->author_email)
            ->paginate();

        // The dataset should have only returned 1 item.
        $this->assertCount(1, $paginator->items());

        // That 1 item should be the expected recipe.
        $this->assertSame($expectedRecipe->id, $paginator->items()[0]->id);
    }

    public function test_set_keyword_correctly_filters_dataset(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $expectedKeyword = 'foobar';

        /** @var Recipe $expectedRecipe */
        $expectedRecipe = $expectedRecipes[1];
        $expectedRecipe->description = $expectedRecipe->description . $expectedKeyword;
        $expectedRecipe->save();

        $dataset = new RecipesDataset();
        $paginator = $dataset->setKeyword($expectedKeyword)
            ->paginate();

        // The dataset should have only returned 1 item.
        $this->assertCount(1, $paginator->items());

        // That 1 item should be the expected recipe.
        $this->assertSame($expectedRecipe->id, $paginator->items()[0]->id);
    }

    public function test_set_keyword_applies_filters_to_all_expected_fields(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        /*
         * Create a 5th recipe that won't contain the expected keyword and shouldn't
         * appear in the dataset.
         */
        Recipe::factory()
            ->has(
                Ingredient::factory()->count(3)
            )
            ->has(
                Step::factory()->count(3)
            )
            ->create();

        /*
         * Set each of the expected recipes to have the expected keyword in
         * one of the fields it's expected to search.
         */
        $expectedKeyword = 'foobar';

        // Set the first expected recipe to have the expected keyword in its name.
        $expectedRecipes[0]->name = $expectedRecipes[0]->name . $expectedKeyword;
        $expectedRecipes[0]->save();

        // Set the second expected recipe to have the expected keyword in its description.
        $expectedRecipes[1]->description = $expectedRecipes[1]->description . $expectedKeyword;
        $expectedRecipes[1]->save();

        // Set the third expected recipe to have the expected keyword in one of its ingredients.
        $expectedRecipes[2]->ingredients[2]->description =
            $expectedRecipes[2]->ingredients[2]->description . $expectedKeyword;
        $expectedRecipes[2]->ingredients[2]->save();

        // Set the fourth expected recipe to have the expected keyword in one of its steps.
        $expectedRecipes[3]->steps[3]->description =
            $expectedRecipes[3]->steps[3]->description . $expectedKeyword;
        $expectedRecipes[3]->steps[3]->save();

        $dataset = new RecipesDataset();
        $paginator = $dataset->setKeyword($expectedKeyword)
            ->paginate();

        // The dataset should have returned 4 items.
        $this->assertCount(4, $paginator->items());

        // The returned items should only be the expected recipes.
        /** @var Recipe $actualRecipe */
        foreach ($paginator->items() as $index => $actualRecipe) {
            $this->assertSame($expectedRecipes[$index]->id, $actualRecipe->id);
        }
    }

    public function test_set_ingredient_correctly_filters_dataset(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $expectedKeyword = 'foobar';

        /** @var Recipe $expectedRecipe */
        $expectedRecipe = $expectedRecipes[1];
        $expectedRecipe->ingredients[1]->description
            = $expectedRecipe->ingredients[1]->description . $expectedKeyword;
        $expectedRecipe->ingredients[1]->save();

        $dataset = new RecipesDataset();
        $paginator = $dataset->setKeyword($expectedKeyword)
            ->paginate();

        // The dataset should have only returned 1 item.
        $this->assertCount(1, $paginator->items());

        // That 1 item should be the expected recipe.
        $this->assertSame($expectedRecipe->id, $paginator->items()[0]->id);
    }
}
