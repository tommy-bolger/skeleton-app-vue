<?php

namespace App\Services;

use App\Enums\SortColumns;
use App\Enums\SortDirections;
use App\Models\Recipe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;

class RecipesDataset
{
    public const int MAX_LIMIT = 25;

    private int $limit = 25;

    private int $page = 1;

    private SortColumns $sortColumn = SortColumns::NAME;

    private SortDirections $sortDirection = SortDirections::ASC;

    private ?string $authorEmail = null;

    private ?string $keyword = null;

    private ?string $ingredient = null;

    public function setSort(?SortColumns $sortColumn, ?SortDirections $sortDirection): static
    {
        $this->sortColumn = $sortColumn ?? SortColumns::NAME;
        $this->sortDirection = $sortDirection ?? SortDirections::ASC;

        return $this;
    }

    public function setPagination(int $page, int $limit): static
    {
        if ($page < 1) {
            $page = 1;
        }

        if ($limit < 1 || $limit > 25) {
            $limit = static::MAX_LIMIT;
        }

        $this->page = $page;
        $this->limit = $limit;

        return $this;
    }

    public function setAuthorEmail(?string $authorEmail): static
    {
        $this->authorEmail = $authorEmail;

        return $this;
    }

    public function setKeyword(?string $keyword): static
    {
        $this->keyword = $keyword;

        return $this;
    }

    public function setIngredient(?string $ingredient): static
    {
        $this->ingredient = $ingredient;

        return $this;
    }

    public function paginate(): LengthAwarePaginator
    {
        $query = Recipe::query()->with([
            'ingredients',
            'steps' => function (HasMany $query) {
                $query->orderBy('step_number');
            },
        ]);

        $this->applyAuthorEmailFilter($query);
        $this->applyKeywordFilter($query);
        $this->applyIngredientFilter($query);

        $query->orderBy($this->sortColumn->value, $this->sortDirection->value);

        return $query->paginate(
            perPage: $this->limit,
            page: $this->page
        );
    }

    private function applyAuthorEmailFilter(Builder $query): void
    {
        if (strlen($this->authorEmail) > 0) {
            $query->where('author_email', '=', $this->authorEmail);
        }
    }

    private function applyKeywordFilter(Builder $query): void
    {
        if (strlen($this->keyword) > 0) {
            $keyword = "%$this->keyword%";

            $query->where(function (Builder $query) use ($keyword) {
                $query->where('name', 'like', $keyword)
                    ->orWhere('description', 'like', $keyword)
                    ->orWhereRelation('ingredients', 'description', 'like', $keyword)
                    ->orWhereRelation('steps', 'description', 'like', $keyword);
            });
        }
    }

    private function applyIngredientFilter(Builder $query): void
    {
        if (strlen($this->ingredient) > 0) {
            $ingredient = "%$this->ingredient%";

            $query->whereRelation('ingredients', 'description', 'like', $ingredient);
        }
    }
}
