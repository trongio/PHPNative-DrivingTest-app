<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestResult extends Model
{
    protected $fillable = [
        'user_id',
        'test_template_id',
        'test_type',
        'license_type_id',
        'configuration',
        'questions_with_answers',
        'correct_count',
        'wrong_count',
        'total_questions',
        'score_percentage',
        'status',
        'started_at',
        'finished_at',
        'time_taken_seconds',
    ];

    protected function casts(): array
    {
        return [
            'configuration' => 'array',
            'questions_with_answers' => 'array',
            'score_percentage' => 'decimal:2',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function testTemplate(): BelongsTo
    {
        return $this->belongsTo(TestTemplate::class);
    }

    public function licenseType(): BelongsTo
    {
        return $this->belongsTo(LicenseType::class);
    }

    public function isPassed(): bool
    {
        return $this->status === 'passed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isOvertime(): bool
    {
        return $this->time_taken_seconds !== null && $this->time_taken_seconds < 0;
    }
}
