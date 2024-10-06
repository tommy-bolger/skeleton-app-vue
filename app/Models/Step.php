<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read integer $id
 * @property integer $recipe_id
 * @property string $description
 * @property integer $step_number
 *
 * @property-read Recipe $recipe
 */
class Step extends Model
{
    use HasFactory;

    protected $casts = [
        'recipe_id' => 'integer',
        'description' => 'string',
        'step_number' => 'integer',
    ];

    protected $fillable = [
        'recipe_id',
        'description',
        'step_number',
    ];

    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }
}
