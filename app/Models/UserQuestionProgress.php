<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserQuestionProgress extends Model
{
    protected $table = 'user_question_progress';

    protected $fillable = [
        'user_id',
        'question_id',
        'times_correct',
        'times_wrong',
        'is_bookmarked',
        'is_learned',
        'notes',
        'last_answered_at',
        'first_answered_at',
    ];

    protected function casts(): array
    {
        return [
            'is_bookmarked' => 'boolean',
            'is_learned' => 'boolean',
            'last_answered_at' => 'datetime',
            'first_answered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function accuracyPercentage(): float
    {
        $total = $this->times_correct + $this->times_wrong;

        return $total > 0 ? round(($this->times_correct / $total) * 100, 2) : 0;
    }
}
