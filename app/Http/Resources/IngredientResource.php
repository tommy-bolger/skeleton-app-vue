<?php

namespace App\Http\Resources;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Ingredient $ingredient */
        $ingredient = $this->resource;

        return [
            'description' => $ingredient->description,
        ];
    }
}
