<?php

namespace App\Http\Resources;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecipeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Recipe $recipe */
        $recipe = $this->resource;

        return [
            'slug' => $recipe->slug,
            'name' => $recipe->name,
            'description' => $recipe->description,
            'author_email' => $recipe->author_email,
            'ingredients' => IngredientResource::collection($recipe->ingredients),
            'steps' => StepResource::collection($recipe->steps),
        ];
    }
}
