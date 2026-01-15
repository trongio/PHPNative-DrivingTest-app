<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TestTemplate extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'license_type_id',
        'question_count',
        'time_per_question',
        'failure_threshold',
        'category_ids',
        'excluded_question_ids',
    ];

    protected function casts(): array
    {
        return [
            'category_ids' => 'array',
            'excluded_question_ids' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function licenseType(): BelongsTo
    {
        return $this->belongsTo(LicenseType::class);
    }

    public function testResults(): HasMany
    {
        return $this->hasMany(TestResult::class);
    }

    public function totalTimeInSeconds(): int
    {
        return $this->question_count * $this->time_per_question;
    }

    public function maxAllowedWrong(): int
    {
        return (int) floor($this->question_count * ($this->failure_threshold / 100));
    }
}
