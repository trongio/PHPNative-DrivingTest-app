<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Sign extends Model
{
    protected $fillable = [
        'sign_category_id',
        'position',
        'is_child',
        'image',
        'title',
        'title_en',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_child' => 'boolean',
        ];
    }

    public function signCategory(): BelongsTo
    {
        return $this->belongsTo(SignCategory::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }
}
