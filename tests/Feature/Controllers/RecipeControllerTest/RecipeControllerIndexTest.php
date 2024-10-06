<?php

namespace Tests\Feature\Controllers\RecipeControllerTest;

use App\Enums\SortColumns;
use App\Enums\SortDirections;
use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\SetsUpForRecipesDatasetTest;
use Tests\TestCase;

class RecipeControllerIndexTest extends TestCase
{
    use RefreshDatabase;
    use SetsUpForRecipesDatasetTest;
    use SharedRecipeControllerTestMethods;

    private const string API_ROUTE_NAME = 'recipes.index';

    private function getExpectedRecipes(Collection $recipes): array
    {
        $expectedRecipes = [];

        /** @var Recipe $recipe */
        foreach ($recipes as $recipe) {
            $expectedRecipes[] = $this->getExpectedRecipe($recipe);
        }

        return $expectedRecipes;
    }

    public function test_index_returns_correct_response(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $response = $this->getJson(route(static::API_ROUTE_NAME));
        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use ($expectedRecipes) {
            $json->where('data', $this->getExpectedRecipes($expectedRecipes));
            $json->has('links', function (AssertableJson $json) {
                $json->has('first')
                    ->has('last')
                    ->has('prev')
                    ->has('next');
            });
            $json->has('meta', function (AssertableJson $json) {
                $json->has('current_page')
                    ->has('from')
                    ->has('last_page')
                    ->has('links')
                    ->has('path')
                    ->has('per_page')
                    ->has('to')
                    ->has('total');
            });
        });
    }

    public function test_index_sorts_response_in_ascending_order_for_each_sortable_column(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        foreach (SortColumns::cases() as $sortColumn) {
            $response = $this->getJson(route(static::API_ROUTE_NAME, [
                'sort' => $sortColumn->value,
                'sort_direction' => SortDirections::ASC->value,
            ]));
            $response->assertOk();

            $response->assertJson(function (AssertableJson $json) use ($expectedRecipes, $sortColumn) {
                $json->where('data', $this->getExpectedRecipes(
                    $expectedRecipes->sortBy($sortColumn->value)
                        ->values()
                ));
                $json->etc();
            });
        }
    }

    public function test_index_sorts_response_in_descending_order_for_each_sortable_column(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        foreach (SortColumns::cases() as $sortColumn) {
            $response = $this->getJson(route(static::API_ROUTE_NAME, [
                'sort' => $sortColumn->value,
                'sort_direction' => SortDirections::DESC->value,
            ]));
            $response->assertOk();

            $response->assertJson(function (AssertableJson $json) use ($expectedRecipes, $sortColumn) {
                $json->where('data', $this->getExpectedRecipes(
                    $expectedRecipes->sortByDesc($sortColumn->value)
                        ->values()
                ));
                $json->etc();
            });
        }
    }

    public function test_index_paginates_response_correctly(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        $response = $this->getJson(route(static::API_ROUTE_NAME, [
            'page' => 2,
            'limit' => 2,
        ]));
        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use ($expectedRecipes) {
            $json->where('data', $this->getExpectedRecipes($expectedRecipes->slice(2)));
            $json->etc();
        });
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

        $response = $this->getJson(route(static::API_ROUTE_NAME, [
            'author_email' => 'foo@bar.test',
        ]));
        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use ($expectedRecipe) {
            $json->where('data', $this->getExpectedRecipes(new Collection([$expectedRecipe])));
            $json->etc();
        });
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

        $response = $this->getJson(route(static::API_ROUTE_NAME, [
            'keyword' => $expectedKeyword,
        ]));
        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use ($expectedRecipe) {
            $json->where('data', $this->getExpectedRecipes(new Collection([$expectedRecipe])));
            $json->etc();
        });
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

        $response = $this->getJson(route(static::API_ROUTE_NAME, [
            'ingredient' => $expectedKeyword,
        ]));
        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use ($expectedRecipe) {
            $json->where('data', $this->getExpectedRecipes(new Collection([$expectedRecipe])));
            $json->etc();
        });
    }
}
