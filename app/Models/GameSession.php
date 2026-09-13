<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSession extends Model
{
    use HasFactory;

    public const STATUS_WAITING = 'waiting';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_FINISHED = 'finished';

    protected $fillable = [
        'quiz_id',
        'pin',
        'status',
        'current_question_index',
        'settings',
        'current_question_started_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'current_question_started_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    public static function generateUniquePin(): string
    {
        do {
            $pin = (string) random_int(100000, 999999);
        } while (self::where('pin', $pin)->exists());

        return $pin;
    }

    public function timerSecondsPerQuestion(): int
    {
        return (int) ($this->settings['timer_seconds_per_question'] ?? 30);
    }

    public function basePoints(): int
    {
        return (int) ($this->settings['base_points'] ?? 1000);
    }

    public function isInProgress(): bool
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }
}
