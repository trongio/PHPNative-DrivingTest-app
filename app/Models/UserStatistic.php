<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserStatistic extends Model
{
    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'total_tests_taken',
        'total_tests_passed',
        'total_tests_failed',
        'total_questions_answered',
        'total_correct_answers',
        'overall_accuracy',
        'current_streak_days',
        'best_streak_days',
        'last_activity_date',
        'total_study_time_seconds',
    ];

    protected function casts(): array
    {
        return [
            'overall_accuracy' => 'decimal:2',
            'last_activity_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function passRate(): float
    {
        if ($this->total_tests_taken === 0) {
            return 0;
        }

        return round(($this->total_tests_passed / $this->total_tests_taken) * 100, 2);
    }

    public function formattedStudyTime(): string
    {
        $hours = floor($this->total_study_time_seconds / 3600);
        $minutes = floor(($this->total_study_time_seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}სთ {$minutes}წთ";
        }

        return "{$minutes}წთ";
    }
}
