<?php

namespace App\Http\Controllers;

use App\Enums\SortColumns;
use App\Enums\SortDirections;
use App\Http\Resources\RecipeResource;
use App\Models\Recipe;
use App\Services\RecipesDataset;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $dataset = (new RecipesDataset())
            ->setPagination(
                page: $request->integer('page', 1),
                limit: $request->integer('limit')
            )
            ->setSort(
                sortColumn: $request->enum('sort', SortColumns::class),
                sortDirection: $request->enum('sort_direction', SortDirections::class)
            )
            ->setAuthorEmail($request->get('author_email'))
            ->setKeyword($request->get('keyword'))
            ->setIngredient($request->get('ingredient'));

        return RecipeResource::collection($dataset->paginate());
    }

    public function view(string $slug): RecipeResource
    {
        $recipe = Recipe::query()->where('slug', '=', $slug)
            ->firstOrFail();

        return new RecipeResource($recipe);
    }
}
