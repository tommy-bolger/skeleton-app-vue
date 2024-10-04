<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read integer $id
 * @property integer $recipe_id
 * @property string $description
 *
 * @property-read Recipe $recipe
 */
class Ingredient extends Model
{
    use HasFactory;

    protected $casts = [
        'recipe_id' => 'integer',
        'description' => 'string',
    ];

    protected $fillable = [
        'recipe_id',
        'description',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
