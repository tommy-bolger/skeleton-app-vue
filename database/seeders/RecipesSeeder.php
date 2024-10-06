<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\Recipe;
use App\Models\Step;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RecipesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dummy recipes were provided from https://dummyjson.com/docs/recipes
        $dummyRecipeData = json_decode(file_get_contents(__DIR__ . '/recipes.json'), true);

        DB::beginTransaction();

        foreach ($dummyRecipeData['recipes'] as $dummyRecipe) {
            $ingredients = (new Collection($dummyRecipe['ingredients']))->map(fn($item) => [
                'description' => $item
            ]);

            $steps = (new Collection($dummyRecipe['instructions']))->map(fn($item, $index) => [
                'description' => $item,
                'step_number' => $index + 1,
            ]);

            Recipe::factory()
                ->has(
                    Ingredient::factory()->count($ingredients->count())
                        ->sequence(...$ingredients->toArray())
                )
                ->has(
                    Step::factory()->count($steps->count())
                        ->sequence(...$steps->toArray())
                )
                ->create([
                    'name' => $dummyRecipe['name'],
                    'slug' => Str::slug($dummyRecipe['name']),
                ]);
        }

        DB::commit();
    }
}
