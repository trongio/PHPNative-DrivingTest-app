<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SignCategory extends Model
{
    protected $fillable = [
        'name',
        'group_number',
    ];

    public function signs(): HasMany
    {
        return $this->hasMany(Sign::class);
    }
}
