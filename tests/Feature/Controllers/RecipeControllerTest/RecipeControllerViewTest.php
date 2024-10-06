<?php

namespace Tests\Feature\Controllers\RecipeControllerTest;

use App\Models\Recipe;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\Feature\Traits\SetsUpForRecipesDatasetTest;
use Tests\TestCase;

class RecipeControllerViewTest extends TestCase
{
    use RefreshDatabase;
    use SetsUpForRecipesDatasetTest;
    use SharedRecipeControllerTestMethods;

    private const string API_ROUTE_NAME = 'recipes.view';

    public function test_view_returns_correct_response(): void
    {
        [
            'expectedRecipes' => $expectedRecipes
        ] = $this->setupForRecipesDatasetTests();

        /** @var Recipe $expectedRecipe */
        $expectedRecipe = $expectedRecipes[1];

        $response = $this->getJson(route(static::API_ROUTE_NAME, [
            'slug' => $expectedRecipe->slug,
        ]));
        $response->assertOk();

        $response->assertJson(function (AssertableJson $json) use ($expectedRecipe) {
            $json->where('data', $this->getExpectedRecipe($expectedRecipe));
        });
    }

    public function test_view_returns_404_when_slug_does_not_exist_in_recipes_table(): void
    {
        $this->getJson(route(static::API_ROUTE_NAME, [
            'slug' => 'foobar',
        ]))
            ->assertNotFound();
    }
}
