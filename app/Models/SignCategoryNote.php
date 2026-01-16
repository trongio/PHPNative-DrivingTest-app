<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignCategoryNote extends Model
{
    protected $fillable = [
        'sign_category_id',
        'position',
        'content',
        'sign_ids',
    ];

    protected function casts(): array
    {
        return [
            'sign_ids' => 'array',
        ];
    }

    public function signCategory(): BelongsTo
    {
        return $this->belongsTo(SignCategory::class);
    }
}
