<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property-read integer $id
 * @property string $name
 * @property string $description
 * @property string $author_email
 * @property string $slug
 *
 * @property-read Collection<Ingredient[]> $ingredients
 * @property-read Collection<Step[]> $steps
 */
class Recipe extends Model
{
    use HasFactory;

    protected $casts = [
        'name' => 'string',
        'description' => 'string',
        'author_email' => 'string',
        'slug' => 'string',
    ];

    protected $fillable = [
        'name',
        'description',
        'author_email',
        'slug',
    ];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }
}
